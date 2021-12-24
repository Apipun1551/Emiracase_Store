<div class="row">
    @forelse ($products as $product)
        @include('themes.ezone.products.grid_box')
    @empty
        No product found!
    @endforelse
</div>
© 2021 GitHub, Inc.
