<div class="flex-col px-4 py-5 space-y-4 bg-white rounded-md sm:p-6">
  <x-hub::input.group :label="__('adminhub::inputs.name')" for="name" :error="$errors->first('currency.name')">
    <x-hub::input.text wire:model.defer="shippingZone.name" name="name" id="name" :error="$errors->first('currency.name')" />
  </x-hub::input.group>

  <x-hub::input.group label="Type" for="type"  :error="$errors->first('currency.name')">
    <x-hub::input.select id="type" wire:model.defer="shippingZone.type">
      <option value="countries">Limit to Countries</option>
      <option value="states">Limit to States / Provinces</option>
      <option value="postcodes">Limit to list of Postcodes</option>
    </x-hub::input.select>
  </x-hub::input.group>

  <x-hub::input.country-select wire:model="countryPlaceholder" wire:change="selectCountry($event.target.value)" />
</div>
<div class="px-4 py-3 text-right bg-gray-50 sm:px-6">
  <button type="submit" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
    @if($shippingZone->id)
      Save shipping zone
    @else
      Create shipping zone
    @endif
  </button>
</div>