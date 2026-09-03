@php
    $currentRouteName = request()->route()->getName();
@endphp


<div class="tw:flex tw:justify-between">
    <div class="tw:min-h-8">
        <div class="tw:inline-block tw:p-1">
            <div class="tw:inline-block tw:p-1" style="border-bottom: 0">
                <span style="font-weight: bold">{{ $info['title'] }}</span> »

                @if ($currentRouteName === 'plugin.page')
                    <span class="pagemenu-selected">
                @endif

                @if ($currentRouteName !== 'plugin.page')
                    <span>
                @endif

                <a href="{{ route('plugin.page', 'device-importer') }}" class="sync-filter-url">Overview</a>
                </span>
                |

                @if ($currentRouteName === 'device-importer.settings')
                    <span class="pagemenu-selected">
                @endif

                @if ($currentRouteName !== 'device-importer.settings')
                    <span>
                @endif

                <a href="{{ route('device-importer.settings') }}" class="sync-filter-url">Settings</a>
                </span>
                @if ($info['redis'])
                    |

                    @if ($currentRouteName === 'device-importer.upload')
                        <span class="pagemenu-selected">
                    @endif

                    @if ($currentRouteName !== 'device-importer.upload')
                        <span>
                    @endif

                    <a href="{{ route('device-importer.upload') }}" class="sync-filter-url">Upload</a>
                    </span>
                @endif

            </div>
        </div>
    </div>
</div>
