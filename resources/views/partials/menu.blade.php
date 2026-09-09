@php
    $currentRouteName = request()->route()->getName();
@endphp


<div class="tw:flex tw:justify-between">
    <div class="tw:min-h-8">
        <div class="tw:inline-block tw:p-1">
            <div class="tw:inline-block tw:p-1" style="border-bottom: 0">
                <span style="font-weight: bold">{{ $info['title'] }}</span> »



                @if ($currentRouteName === 'plugin.page')
                    <span class="pagemenu-selected" style="margin-right: 4px">
                @endif

                @if ($currentRouteName !== 'plugin.page')
                    <span>
                @endif

                <a href="{{ route('plugin.page', 'device-importer') }}" class="sync-filter-url">Plugin</a>
                </span>

                @can('access-admin')
                    |

                    @if ($currentRouteName === 'device-importer.settings')
                        <span class="pagemenu-selected" style="margin-right: 4px">
                    @endif

                    @if ($currentRouteName !== 'device-importer.settings')
                        <span>
                    @endif

                    <a href="{{ route('device-importer.settings') }}" class="sync-filter-url">Settings</a>
                    </span>
                    @if (!isset($info['dbStatus']['error']))
                        |@if ($currentRouteName === 'device-importer.export')
                            <span class="pagemenu-selected" style="margin-right: 4px">
                        @endif

                        <a href="{{ route('device-importer.export') }}" class="sync-filter-url">Export</a>
                        @if ($currentRouteName !== 'device-importer.export')
                            <span>
                        @endif
                        </span>
                        |

                        @if ($currentRouteName === 'device-importer.upload')
                            <span class="pagemenu-selected" style="margin-right: 4px">
                        @endif

                        @if ($currentRouteName !== 'device-importer.upload')
                            <span>
                        @endif

                        <a href="{{ route('device-importer.upload') }}" class="sync-filter-url">Upload</a>
                        </span>
                    @endif
                @endcan
            </div>
        </div>
    </div>
</div>
