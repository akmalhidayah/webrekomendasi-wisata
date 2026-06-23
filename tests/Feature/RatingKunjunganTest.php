<?php

namespace Tests\Feature;

use App\Models\RatingKunjungan;
use App\Models\User;
use App\Models\Wisata;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RatingKunjunganTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_submit_valid_visit_rating_as_approved(): void
    {
        $this->seed();
        $wisata = Wisata::firstOrFail();

        $this->from(route('wisatawan.wisata.show', $wisata->slug))
            ->post(route('wisatawan.rating-kunjungan.store'), [
                'wisata_id' => $wisata->id,
                'pernah_dikunjungi' => true,
                'rating' => 5,
                'ulasan' => 'Tempatnya menarik dan bersih.',
            ])->assertRedirect(route('wisatawan.wisata.show', $wisata->slug));

        $this->assertDatabaseHas('rating_kunjungan', [
            'wisata_id' => $wisata->id,
            'rating' => 5,
            'status' => 'disetujui',
        ]);
        $this->assertDatabaseCount('guest_visitors', 1);
    }

    public function test_admin_can_approve_a_rating(): void
    {
        [$admin, $rating] = $this->createPendingRating();

        $this->actingAs($admin)
            ->patch(route('admin.rating-kunjungan.setujui', $rating))
            ->assertSessionHas('success');

        $this->assertSame('disetujui', $rating->fresh()->status);
        $this->get(route('admin.rating-kunjungan.index'))->assertOk();
        $this->get(route('admin.rating-kunjungan.show', $rating))->assertOk();
        $this->get(route('wisatawan.wisata.show', $rating->wisata->slug))
            ->assertOk()
            ->assertSee('1 ulasan');
    }

    public function test_admin_can_reject_a_rating(): void
    {
        [$admin, $rating] = $this->createPendingRating();

        $this->actingAs($admin)
            ->patch(route('admin.rating-kunjungan.tolak', $rating))
            ->assertSessionHas('success');

        $this->assertSame('ditolak', $rating->fresh()->status);
    }

    private function createPendingRating(): array
    {
        $this->seed();
        $rating = RatingKunjungan::create([
            'wisata_id' => Wisata::firstOrFail()->id,
            'rating' => 4,
            'ulasan' => 'Bagus.',
            'pernah_dikunjungi' => true,
            'status' => 'pending',
        ]);

        return [User::where('email', 'admin@gmail.com')->firstOrFail(), $rating];
    }
}
