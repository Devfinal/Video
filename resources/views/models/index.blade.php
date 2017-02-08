@extends('layouts.main')

@section('content')
    <div class="row model-list">
        @foreach ($models as $model)
            <div class="col-xl-3 col-md-4 col-xs-6">
                <?php $backgroundImage = isset($model->profile->cover) ? 'style="background-image: url(' . Storage::url($model->profile->cover->path) . ')"' : '' ?>
                <div class="card card-inverse">
                    <div class="card-block text-xs-center text-white" {!! $backgroundImage !!}>
                        <div class="card-title">
                            {{ $model->username }}
                        </div>
                    </div>
                    <div class="card-footer bg-inverse text-xs-center">
                        {{ $model->username }}
                    </div>

                    <a class="link-wrapper" href="{{ route('models.show', $model) }}"></a>
                </div>
            </div>
        @endforeach
    </div>
@endsection
