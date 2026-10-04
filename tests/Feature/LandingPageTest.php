<?php

declare(strict_types=1);

it('renders public landing page with brand copy and correct fee rules', function (): void {
    $response = $this->get('/');

    $response->assertOk()
        ->assertSee('IuranKita')
        ->assertSee('Bayar Iuran, Tertib Administrasi, Nyaman Bertetangga.')
        ->assertSee('Del Mattappa Residence')
        ->assertSee('Kelola Iuran Warga Lebih Mudah.')
        ->assertSee('Satu kegiatan pembangunan, satu kali iuran.')
        ->assertSee('Rp50.000')
        ->assertSee('Rp35.000')
        ->assertSee('Rp100.000')
        ->assertSee('49-light.png')
        ->assertSee('49-dark.png')
        ->assertSee('49-favicon.png')
        ->assertSee('SEKALI BAYAR')
        ->assertSee('Layanan Mandiri Warga')
        ->assertSee('Cek Tagihan dan Kwitansi Iuran')
        ->assertSee('Denah Perumahan Del Mattappa Residence')
        ->assertSee('denah-del-mattappa.png');
});

it('renders receipt print view with valid transaction details', function (): void {
    $user = App\Models\User::factory()->create();
    $payment = App\Models\Payment::factory()->create();

    $this->actingAs($user)
        ->get(route('receipt.print', $payment))
        ->assertOk()
        ->assertSee($payment->receipt_number)
        ->assertSee('Bukti Pembayaran')
        ->assertSee('Telah Diterima Dari');
});

it('allows guests to view and verify digital receipt via public URL', function (): void {
    $payment = App\Models\Payment::factory()->create();

    $this->get(route('receipt.print', $payment))
        ->assertOk()
        ->assertSee($payment->receipt_number)
        ->assertSee('Scan untuk Verifikasi');
});
