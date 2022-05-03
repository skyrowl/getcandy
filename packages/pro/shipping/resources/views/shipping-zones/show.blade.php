<div class="flex-col px-8 space-y-4 md:px-12">
  <div class="space-y-4">
    <form action="#" wire:submit.prevent="save" class="shadow sm:rounded-md">
      @include('shipping::partials.forms.shipping-zone')
    </form>
    <div class="shadow sm:rounded-md">
      <div class="flex-col px-4 py-5 space-y-4 bg-white rounded-md sm:p-6">
        <header class="flex items-center justify-between">
          <h3 class="text-lg font-medium leading-6 text-gray-900">
            Shipping Methods
          </h3>

          <div>
            <x-hub::dropdown value="Add shipping method">
              <x-slot name="options">
                @foreach($this->supportedShippingMethods as $shippingMethod)
                  <x-hub::dropdown.button type="button" wire:click="addShippingMethod('{{ $shippingMethod['key'] }}')">
                    {{ $shippingMethod['name'] }}
                  </x-hub::dropdown.button>
                @endforeach
              </x-slot>
            </x-hub::dropdown>
          </div>
        </header>

        <div class="space-y-4">
          @foreach($this->shippingMethods as $key => $method)
            <div class="flex items-center justify-between pb-4 border-b" wire:key="{{ $key }}">
              <div class="grow">
                @if($method['custom_name'])
                  <div>
                    <strong>{{ $method['custom_name'] }}</strong>
                    <small class="text-gray-500">({{ $method['name'] }})</small>
                  </div>
                @else
                  <strong>{{ $method['name'] }}</strong>
                @endif
                <p class="text-sm text-gray-500">{{ $method['custom_description'] ?: $method['description'] }}</p>
              </div>

              <div class="ml-4">
                <x-hub::input.toggle :on="$method['enabled']" wire:click="toggleMethod('{{ $key }}')" />
              </div>

              @if($method['method_id'] && $method['enabled'])
                <div class="ml-4">
                  <x-hub::button wire:click="$set('methodToEdit', '{{ $key }}')">Edit</x-hub::button>
                </div>
              @endif

              <div @if($methodToEdit != $key) class="hidden" @endif>
                <x-hub::slideover title="Free Shipping" wire:model="methodToEdit">
                  @livewire($method['component'], [
                    'shippingMethodId' => $method['method_id'],
                    'shippingZone' => $shippingZone,
                  ], key('shipping_method_'.$key))
                </x-hub::slideover>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>

  {{-- @include('shipping::partials.ship-by-total')
  @include('shipping::partials.free-shipping')
  @include('shipping::partials.flat-rate') --}}
</div>
