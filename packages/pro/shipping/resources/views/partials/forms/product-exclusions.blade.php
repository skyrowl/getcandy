<div>
  <div class="flex items-center justify-between">
    <span>Exclude Products</span>
    <div>
      <x-hub::button size="sm" theme="gray">Add product</x-hub::button>
    </div>
  </div>

  <div class="mt-4 space-y-2">
    @foreach($this->exclusions as $exclusion)
      <div class="flex items-center justify-between p-2 border rounded">
        <div class="flex items-center">
          <img src="{{ $exclusion->purchasable->thumbnail->getUrl('small') }}" class="w-6 mr-3 rounded">
          {{ $exclusion->purchasable->translateAttribute('name') }}
        </div>
        <div>
          <x-hub::icon ref="trash" class="w-4 text-gray-500 hover:text-red-500" />
        </div>
      </div>

    @endforeach
  </div>
</div>