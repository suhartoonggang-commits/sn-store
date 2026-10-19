<?php

it('renders the storefront with ten products', function () {
    $response = $this->get('/');

    $response->assertOk()
        ->assertViewIs('pages.public.home')
        ->assertSeeText('Produk terbaru')
        ->assertSeeText('Sony Alpha A6400');

    expect(substr_count($response->getContent(), 'data-product-card'))->toBe(10);
});
