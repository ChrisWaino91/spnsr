<x-filament-panels::page>
        {{ $this->infoList }}
        <x-filament::card>
                @foreach ($campaigns as $campaign)
                    <x-filament::fieldset>
                        <x-slot name="label">
                            <a href="{{$campaign->url()}}"> {{$campaign->name}} </a>
                        </x-slot>
                            <div class="relative overflow-x-auto">
                                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                    <thead class="text-xs text-gray-700 bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                        <tr>
                                            <th scope="col" class="px-6 py-3">
                                                Ref
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                Promoted Category
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                Impressions
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                Clicks
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                Cost
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($campaign->promotions as $promotion)
                                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                            <td class="px-6 py-4">
                                                <a href="{{ $promotion->url() }}">
                                                    #{{ $promotion->id }}
                                                </a>
                                            </td>
                                            <td class="px-6 py-4">
                                                {{ $promotion->category->name }}
                                            </td>
                                            <td class="px-6 py-4">
                                                {{ number_format($promotion->impressions->count()) }}
                                            </td>
                                            <td class="px-6 py-4">
                                                {{ number_format($promotion->clicks->count()) }}
                                            </td>
                                            <td class="px-6 py-4">
                                                £{{ number_format($promotion->clicks->count() * $promotion->cost_per_click, 2) }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                        <div class="pt-6 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                            Total:
                        </div>

                        <div class="py-2 flex w-max">
                            <x-filament::badge>
                                <div>£{{ $campaign->spend($record->invoice_date) }}</div>
                            </x-filament::badge>
                        </div>

                    </x-filament::fieldset>

                        <br />
                        <br />

                @endforeach
            </x-filament::card>

</x-filament-panels::page>
