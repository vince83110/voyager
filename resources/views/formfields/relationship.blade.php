@if(isset($options->model) && isset($options->type))

    @if(class_exists($options->model))

        @php
            $relationshipField = $row->field;
        @endphp

        @if($options->type == 'belongsTo')

            @if(isset($view) && ($view == 'browse' || $view == 'read'))

                @php
                    $relationshipData = (isset($data)) ? $data : $dataTypeContent;
                    $model = app($options->model);
                    $query = $model::find($relationshipData->{$options->column});
                @endphp

                @if(isset($query))
                    @if (isset($options->slug))
                        <p>
                            <a href="{{ route('voyager.'. $options->slug .'.show', ['id' => $query->getKey()]) }}" target="_blank">
                                {{ $query->{$options->label} }}
                            </a>
                        </p>
                    @else
                        <p>{{ $query->{$options->label} }}</p>
                    @endif
                @else
                    <p>{{ __('voyager::generic.no_results') }}</p>
                @endif

            @else

                <select
                        class="form-control select2-ajax" name="{{ $options->column }}"
                        data-get-items-route="{{route('voyager.' . $dataType->slug.'.relation')}}"
                        data-get-items-field="{{$row->field}}"
                >
                    @php
                        $model = app($options->model);
                        $query = $model::where($options->key, $dataTypeContent->{$options->column})->get();
                    @endphp

                    @if(!$row->required)
                        <option value="">{{__('voyager::generic.none')}}</option>
                    @endif

                    @foreach($query as $relationshipData)
                        <option value="{{ $relationshipData->{$options->key} }}" @if($dataTypeContent->{$options->column} == $relationshipData->{$options->key}){{ 'selected="selected"' }}@endif>{{ $relationshipData->{$options->label} }}</option>
                    @endforeach
                </select>

            @endif

        @elseif($options->type == 'hasOne')

            @php

                $relationshipData = (isset($data)) ? $data : $dataTypeContent;

                $model = app($options->model);
                $query = $model::where($options->column, '=', $relationshipData->id)->first();

            @endphp

            @if(isset($query))
                @if (isset($options->slug))
                    <p>
                        <a href="{{ route('voyager.'. $options->slug .'.show', ['id' => $query->getKey()]) }}" target="_blank">
                            {{ $query->{$options->label} }}
                        </a>
                    </p>
                @else
                    <p>{{ $query->{$options->label} }}</p>
                @endif
            @else
                <p>{{ __('voyager::generic.no_results') }}</p>
            @endif

        @elseif($options->type == 'hasMany')

            @if(isset($view) && ($view == 'browse' || $view == 'read'))

                @php
                    $relationshipData = (isset($data)) ? $data : $dataTypeContent;
                    $model = app($options->model);

            		$selected_values = $model::where($options->column, '=', $relationshipData->id)->get()->map(function ($item, $key) use ($options) {
            			return array(
                            'label' => $item->{$options->label},
                            'id' => $item->getKey()
            			);
            		})->all();
                @endphp

                @if($view == 'browse')
                    @if(empty($selected_values))
                        <p>{{ __('voyager::generic.no_results') }}</p>
                    @else
                        @foreach(array_slice($selected_values, 0, 4) as $selected_value)
                            <span class="badge label label-default">
                                {{ $selected_value['label'] }}
                            </span>
                        @endforeach
                        @if (count($selected_values) > 4)
                            ...
                        @endif
                    @endif
                @else
                    @if(empty($selected_values))
                        <p>{{ __('voyager::generic.no_results') }}</p>
                    @else
                        <div class="list-group">
                            @foreach($selected_values as $selected_value)
                                @if (isset($options->slug) and $selected_value['id'])
                                    <a href="{{ route('voyager.'. $options->slug .'.show', ['id' => $selected_value['id']]) }}" class="list-group-item list-group-item-action" target="_blank">
                                        {{ $selected_value['label'] }}
                                    </a>
                                @else
                                    <div class="list-group-item">{{ $selected_value['label'] }}</div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                @endif

            @else

                @php
                    $model = app($options->model);
                    $query = $model::where($options->column, '=', $dataTypeContent->id)->get();
                @endphp

                @if(isset($query))
                    <div class="list-group">
                        @foreach($query as $query_res)
                            <div class="list-group-item">{{ $query_res->{$options->label} }}</div>
                        @endforeach
                    </div>

                @else
                    <p>{{ __('voyager::generic.no_results') }}</p>
                @endif

            @endif

        @elseif($options->type == 'belongsToMany')

            @if(isset($view) && ($view == 'browse' || $view == 'read'))

                @php
                    $relationshipData = (isset($data)) ? $data : $dataTypeContent;

                    $selected_values = isset($relationshipData) ? $relationshipData->belongsToMany($options->model, $options->pivot_table)->get()->map(function ($item, $key) use ($options) {
            			return array(
                            'label' => $item->{$options->label},
                            'id' => $item->getKey()
            			);
            		})->all() : array();
                @endphp

                @if($view == 'browse')
                    @if(empty($selected_values))
                        <p>{{ __('voyager::generic.no_results') }}</p>
                    @else
                        @foreach(array_slice($selected_values, 0, 4) as $selected_value)
                            <span class="badge label label-default">
                                {{ $selected_value['label'] }}
                            </span>
                        @endforeach
                        @if (count($selected_values) > 4)
                            ...
                        @endif
                    @endif
                @else
                    @if(empty($selected_values))
                        <p>{{ __('voyager::generic.no_results') }}</p>
                    @else
                        <div class="list-group">
                            @foreach($selected_values as $selected_value)
                                @if (isset($options->slug) and $selected_value['id'])
                                    <a href="{{ route('voyager.'. $options->slug .'.show', ['id' => $selected_value['id']]) }}" class="list-group-item list-group-item-action" target="_blank">
                                        {{ $selected_value['label'] }}
                                    </a>
                                @else
                                    <div class="list-group-item">{{ $selected_value }}</div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                @endif

            @else
                <select
                    class="form-control @if(isset($options->taggable) && $options->taggable == 'on') select2-taggable @else select2-ajax @endif"
                    name="{{ $relationshipField }}[]" multiple
                    data-get-items-route="{{route('voyager.' . $dataType->slug.'.relation')}}"
                    data-get-items-field="{{$row->field}}"
                    @if(isset($options->taggable) && $options->taggable == 'on')
                    data-route="{{ route('voyager.'.str_slug($options->table).'.store') }}"
                    data-label="{{$options->label}}"
                    data-error-message="{{__('voyager::bread.error_tagging')}}"
                    @endif
                >
                    @php
                        $selected_values = isset($dataTypeContent) ? $dataTypeContent->belongsToMany($options->model, $options->pivot_table)->get()->map(function ($item, $key) use ($options) {
                            return $item->{$options->key};
                        })->all() : array();
                        $relationshipOptions = app($options->model)->all();
                    @endphp

                    @if(!$row->required)
                        <option value="">{{__('voyager::generic.none')}}</option>
                    @endif

                    @foreach($relationshipOptions as $relationshipOption)
                        <option value="{{ $relationshipOption->{$options->key} }}" @if(in_array($relationshipOption->{$options->key}, $selected_values)){{ 'selected="selected"' }}@endif>{{ $relationshipOption->{$options->label} }}</option>
                    @endforeach

                </select>

            @endif

        @endif

    @else

        cannot make relationship because {{ $options->model }} does not exist.

    @endif

@endif
