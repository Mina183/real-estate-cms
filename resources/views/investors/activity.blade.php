<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Activity Log: {{ $investor->organization_name ?? $investor->legal_entity_name }}
            </h2>
            <a href="{{ route('investors.show', $investor) }}" 
               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                ← Back to Investor
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Stage Transitions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">📊 Stage Transitions</h3>
                    
                    @if($stageTransitions->count() > 0)
                        <div class="space-y-3">
                            @foreach($stageTransitions as $transition)
                                <div class="border-l-4 border-blue-500 bg-blue-50 p-4 rounded">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2 mb-2">
                                                <span class="px-2 py-1 text-xs font-semibold rounded bg-gray-200 text-gray-700">
                                                    {{ str_replace('_', ' ', ucfirst($transition->from_stage)) }}
                                                </span>
                                                <span class="text-gray-400">→</span>
                                                <span class="px-2 py-1 text-xs font-semibold rounded bg-green-200 text-green-800">
                                                    {{ str_replace('_', ' ', ucfirst($transition->to_stage)) }}
                                                </span>
                                            </div>
                                            @if($transition->reason)
                                                <p class="text-sm text-gray-600 mt-2">
                                                    <strong>Reason:</strong> {{ $transition->reason }}
                                                </p>
                                            @endif
                                        </div>
                                        <div class="text-right text-sm text-gray-500">
                                            <p>{{ $transition->changedBy->name ?? 'System' }}</p>
                                            <p>{{ $transition->transitioned_at->format('M d, Y H:i') }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">No stage transitions yet.</p>
                    @endif
                </div>
            </div>

            <!-- All Activities -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">📋 All Activities</h3>
                    
                    @if($activities->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Activity
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            User
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Details
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            IP Address
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Date/Time
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($activities as $activity)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <span class="px-2 py-1 text-xs font-semibold rounded
                                                    @if($activity->activity_type === 'stage_transition') bg-blue-100 text-blue-800
                                                    @elseif($activity->activity_type === 'view') bg-green-100 text-green-800
                                                    @elseif($activity->activity_type === 'download') bg-purple-100 text-purple-800
                                                    @elseif($activity->activity_type === 'permission_granted') bg-yellow-100 text-yellow-800
                                                    @else bg-gray-100 text-gray-800
                                                    @endif">
                                                    {{ str_replace('_', ' ', ucfirst($activity->activity_type)) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                                {{ $activity->user->name ?? 'System' }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-600">
                                                @if($activity->metadata)
                                                    @if(isset($activity->metadata['from_stage']))
                                                        {{ ucfirst($activity->metadata['from_stage']) }} → {{ ucfirst($activity->metadata['to_stage']) }}
                                                    @endif
                                                    @if(isset($activity->metadata['reason']))
                                                        <br><span class="text-xs italic">{{ $activity->metadata['reason'] }}</span>
                                                    @endif
                                                    @if(isset($activity->metadata['access_level']))
                                                        Access level: {{ $activity->metadata['access_level'] }}
                                                    @endif
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                                {{ $activity->ip_address ?? '—' }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                                {{ $activity->activity_at->format('M d, Y H:i:s') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $activities->links() }}
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">No activity recorded yet.</p>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>