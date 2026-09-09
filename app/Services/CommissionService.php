<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Commission;
use App\Models\Partner;
use Illuminate\Support\Facades\Log;

class CommissionService
{
    /**
     * Record or update commission snapshot for a given booking.
     */
    public static function createOrUpdateForBooking(Booking $booking): ?Commission
    {
        // Check if booking has referral info
        $partner = null;

        if ($booking->partner_id) {
            $partner = Partner::find($booking->partner_id);
        } elseif ($booking->referral_code) {
            $partner = Partner::where('referral_code', strtoupper($booking->referral_code))->first();
            if ($partner) {
                $booking->partner_id = $partner->id;
                $booking->save();
            }
        }

        if (!$partner) {
            return null;
        }

        $tour = $booking->tour;
        if (!$tour) {
            return null;
        }

        $participants            = max(1, (int) $booking->participants);
        $publishPricePerPax     = (float) $tour->price;
        $hppPricePerPax         = (float) ($tour->hpp_price ?? 0);
        $commissionPercentage   = (float) ($tour->commission_percentage ?? 0);

        // Financial snapshot calculation based on formula:
        // Margin = (Publish Price - HPP) * Participants
        // Commission = Margin * (Commission % / 100)
        $marginPerPax   = max(0, $publishPricePerPax - $hppPricePerPax);
        $totalMargin    = $marginPerPax * $participants;
        $commissionAmt  = round(($totalMargin * $commissionPercentage) / 100);

        // Determine initial status: pending until admin confirms booking
        $isConfirmed = in_array($booking->booking_status, ['confirmed', 'completed', 'departed', 'processing']);
        $isPaid      = in_array($booking->payment_status, ['paid', 'dp_paid']);
        $status      = ($isConfirmed && $isPaid) ? 'available' : 'pending';

        if (in_array($booking->booking_status, ['cancelled']) || in_array($booking->payment_status, ['refund', 'failed'])) {
            $status = 'cancelled';
        }

        try {
            $commission = Commission::updateOrCreate(
                ['booking_id' => $booking->id],
                [
                    'partner_id'                      => $partner->id,
                    'tour_id'                         => $tour->id,
                    'hpp_snapshot'                    => $hppPricePerPax,
                    'publish_price_snapshot'          => $publishPricePerPax,
                    'margin_snapshot'                 => $totalMargin,
                    'commission_percentage_snapshot'  => $commissionPercentage,
                    'commission_amount'               => $commissionAmt,
                    'status'                          => $status,
                ]
            );

            return $commission;
        } catch (\Throwable $e) {
            Log::error('Commission calculation error for booking #' . $booking->id . ': ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Synchronize commission status when booking or payment status changes.
     */
    public static function syncStatusForBooking(Booking $booking): void
    {
        $commission = Commission::where('booking_id', $booking->id)->first();

        if (!$commission) {
            // Attempt to create if referral exists
            if ($booking->partner_id || $booking->referral_code) {
                static::createOrUpdateForBooking($booking);
            }
            return;
        }

        // If booking is cancelled or refunded, cancel commission
        if ($booking->booking_status === 'cancelled' || in_array($booking->payment_status, ['refund', 'failed'])) {
            $commission->status = 'cancelled';
            $commission->save();
            return;
        }

        $isConfirmed = in_array($booking->booking_status, ['confirmed', 'completed', 'departed', 'processing']);
        $isPaid      = in_array($booking->payment_status, ['paid', 'dp_paid']);

        // Promote to 'available' ONLY when admin confirms booking and payment
        if ($isConfirmed && $isPaid) {
            if (in_array($commission->status, ['pending', 'approved'])) {
                $commission->status = 'available';
                $commission->save();
            }
        } else {
            // If not yet confirmed or reset to pending
            if ($commission->status === 'available') {
                $commission->status = 'pending';
                $commission->save();
            }
        }
    }
}
