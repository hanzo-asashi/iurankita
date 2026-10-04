<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\PaymentConfirmationStatus;
use App\Models\Invoice;
use App\Models\PaymentConfirmation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class PublicPaymentConfirmationController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'invoice_id' => ['required', 'exists:invoices,id'],
            'sender_name' => ['required', 'string', 'max:100'],
            'sender_bank' => ['required', 'string', 'max:50'],
            'amount' => ['required', 'integer', 'min:1000'],
            'payment_date' => ['required', 'date'],
            'proof_file' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        /** @var Invoice $invoice */
        $invoice = Invoice::query()->with('household')->findOrFail($validated['invoice_id']);

        if ($invoice->balance <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tagihan ini telah lunas sebelumnya.',
            ], 422);
        }

        $proofPath = $request->file('proof_file')->store('payment-proofs', 'public');

        $confirmation = PaymentConfirmation::create([
            'household_id' => $invoice->household_id,
            'invoice_id' => $invoice->id,
            'sender_name' => $validated['sender_name'],
            'sender_bank' => $validated['sender_bank'],
            'amount' => (int) $validated['amount'],
            'payment_date' => $validated['payment_date'],
            'proof_path' => $proofPath,
            'notes' => $validated['notes'] ?? null,
            'status' => PaymentConfirmationStatus::Pending,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Konfirmasi pembayaran berhasil dikirim. Pengurus akan segera memverifikasi dan menerbitkan kwitansi resmi Anda.',
            'confirmation_id' => $confirmation->id,
        ]);
    }
}
