@extends('layouts.main')

@push('scripts-body')
<script>
    var $input = $('.media-upload');
    var $mediaPreviews = $('.media-previews');

    $input.on('change', event => {
        $mediaPreviews.removeClass('bordered').html('');
        $input.closest('.form-group').removeClass('has-danger').find('.upload-error').remove();

        if (event.currentTarget.files.length > 0) {
            $mediaPreviews.addClass('bordered').html('<div class="loader"><i class="fa fa-refresh fa-spin"></i></div>');
            $mediaPreviews.css('min-height', '4em');

            async.eachOfSeries(event.currentTarget.files, (file, index, done) => {
                var fileReader = new FileReader;

                if (file.type.match('image')) {
                    fileReader.addEventListener('loadend', event => {
                        $mediaPreviews.append(
                                '<div class="col-xs-6 col-lg-3">' +
                                '<div class="card">' +
                                '<div style="position:relative">' +
                                '<img src="' + fileReader.result + '" class="card-img-top img-fluid">' +
                                '</div>' +
                                '</div>' +
                                '</div>'
                        );

                        done();
                    }, {once: true});

                    fileReader.readAsDataURL(file);
                } else if (file.type.match('video')) {
                    var video = document.createElement('video');
                    var canvas = document.createElement('canvas');
                    var url;

                    video.preload = 'metadata';
                    video.muted = true;
                    video.playsInline = true;

                    video.addEventListener('canplay', event => {
                        event.currentTarget.currentTime = event.currentTarget.duration / 10;
                    }, {once: true});

                    video.addEventListener('seeked', event => {
                        canvas.width = event.currentTarget.videoWidth;
                        canvas.height = event.currentTarget.videoHeight;
                        canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);

                        var imageData = canvas.toDataURL();

                        $mediaPreviews.append(
                                '<div class="col-xs-6 col-lg-3">' +
                                '<div class="card">' +
                                '<div style="position:relative">' +
                                '<img src="' + imageData + '" class="card-img-top img-fluid">' +
                                '<i class="fa fa-play-circle-o fa-4x play-icon"></i>' +
                                '</div>' +
                                '</div>' +
                                '</div>'
                        );

                        video.pause();
                        URL.revokeObjectURL(url);

                        done();
                    }, {once: true});

                    video.addEventListener('error', event => {
                        video.pause();

                        done({message: 'Error loading content', status: 404});
                    }, {once: true});

                    fileReader.addEventListener('loadend', event => {
                        url = URL.createObjectURL(new Blob([event.currentTarget.result], {type: file.type}));

                        video.src = url;
                        video.play();
                    }, {once: true});

                    fileReader.readAsArrayBuffer(file);
                }
            }, err => {
                if (err) {
                    $mediaPreviews.removeClass('bordered').html('');
                    $input.replaceWith($input.val('').clone(true));
                    $input = $('.media-upload');

                    if (err.status === 404) {
                        $input.closest('.form-group').addClass('has-danger').append(
                                '<div class="form-control-feedback upload-error">' +
                                'An error occurred when loading the content, try uploading video files one by one' +
                                '</div>'
                        );
                    }
                }

                $mediaPreviews.find('.loader').remove();
                $mediaPreviews.css('min-height', 0);
            });
        }
    });
</script>
<script>
    var $form = $('#register-form');
    var $input = $('#media');
    var $modal = $('#progress-modal');
    var $progressbar = $modal.find('.progress');

    $form.on('submit', event => {
        var containsVideo = false;
        var fileList = $input.get(0).files;

        for (var i = 0, l = fileList.length; i < l; i++) {
            if (fileList.item(i).type.match('video')) {
                containsVideo = true;

                break;
            }
        }

        if (containsVideo) {
            var xhr = new XMLHttpRequest;

            if (!(xhr && 'upload' in xhr && 'onprogress' in xhr.upload) || !window.FormData) {
                return;
            }

            event.preventDefault();

            xhr.upload.addEventListener('loadstart', event => {
                $modal.modal({
                    backdrop: 'static',
                    keyboard: false
                });
            });

            xhr.upload.addEventListener('progress', event => {
                $progressbar.val((event.loaded / event.total) * 100);
            });

            xhr.upload.addEventListener('load', event => {
                $modal.find('.modal-body').append(
                        '<p class="text-muted">Processing video, this may take a while...</p>' +
                        '<div class="p-4" style="position:relative">' +
                            '<div class="loader">' +
                                '<i class="fa fa-refresh fa-spin text-muted"></i>' +
                            '</div>' +
                        '</div>'
                );
            });

            xhr.addEventListener('readystatechange', event => {
                if (event.currentTarget.readyState === 4) {
                    if (event.currentTarget.status === 200 && window.location.href !== event.currentTarget.responseURL) {
                        window.location = event.currentTarget.responseURL;
                    } else {
                        document.open();
                        document.write(event.currentTarget.responseText);
                        document.close();
                    }
                }
            });

            xhr.open(event.currentTarget.getAttribute('method'), event.currentTarget.getAttribute('action'), true);
            xhr.send(new FormData(event.currentTarget));
        }
    });
</script>
@endpush

@section('content')
    <div class="container">
        <h2 class="text-uppercase mb-0">Become a model</h2>
        <p class="text-muted mb-4">
            Fields marked with a <i class="fa fa-eye"></i> might be publicly visible
        </p>
        <div class="row">

            <div class="col-md-8">
                <form id="register-form"
                      method="POST"
                      action="{{ url('/models/register') }}"
                      enctype="multipart/form-data"
                      novalidate>
                    {{ csrf_field() }}

                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group{{ $errors->has('email') ? ' has-danger' : '' }}">
                                <label for="email" class="form-control-label">E-Mail Address</label>
                                <input id="email"
                                       type="email"
                                       class="form-control{{ $errors->has('email') ? ' form-control-danger' : '' }}"
                                       name="email"
                                       value="{{ old('email') }}"
                                       required
                                       autofocus
                                       autocomplete="off">

                                @if ($errors->has('email'))
                                    <div class="form-control-feedback">
                                        {{ $errors->first('email') }}
                                    </div>
                                @endif
                            </div>

                            <div class="form-group{{ $errors->has('phone') ? ' has-danger' : '' }}">
                                <label for="phone" class="form-control-label">Phone number</label>
                                <input id="phone"
                                       type="text"
                                       class="form-control{{ $errors->has('phone') ? ' form-control-danger' : '' }}"
                                       name="phone"
                                       value="{{ old('phone') }}"
                                       required
                                       autocomplete="off">

                                @if ($errors->has('phone'))
                                    <div class="form-control-feedback">
                                        {{ $errors->first('phone') }}
                                    </div>
                                @endif
                            </div>

                            <div class="form-group{{ $errors->has('ssn') ? ' has-danger' : '' }}">
                                <label for="ssn" class="form-control-label">Social security number</label>
                                <input id="ssn"
                                       type="text"
                                       class="form-control{{ $errors->has('ssn') ? ' form-control-danger' : '' }}"
                                       name="ssn"
                                       value="{{ old('ssn') }}"
                                       required
                                       autocomplete="off">

                                @if ($errors->has('ssn'))
                                    <div class="form-control-feedback">
                                        {{ $errors->first('ssn') }}
                                    </div>
                                @endif
                            </div>

                            <div class="form-group{{ $errors->has('first_name') ? ' has-danger' : '' }}">
                                <label for="first_name" class="form-control-label">First name</label>
                                <input id="first_name"
                                       type="text"
                                       class="form-control{{ $errors->has('first_name') ? ' form-control-danger' : '' }}"
                                       name="first_name"
                                       value="{{ old('first_name') }}"
                                       required
                                       autocomplete="off">

                                @if ($errors->has('first_name'))
                                    <div class="form-control-feedback">
                                        {{ $errors->first('first_name') }}
                                    </div>
                                @endif
                            </div>

                            <div class="form-group{{ $errors->has('last_name') ? ' has-danger' : '' }}">
                                <label for="last_name" class="form-control-label">Last name</label>
                                <input id="last_name"
                                       type="text"
                                       class="form-control{{ $errors->has('last_name') ? ' form-control-danger' : '' }}"
                                       name="last_name"
                                       value="{{ old('last_name') }}"
                                       required
                                       autocomplete="off">

                                @if ($errors->has('last_name'))
                                    <div class="form-control-feedback">
                                        {{ $errors->first('last_name') }}
                                    </div>
                                @endif
                            </div>

                            <div class="form-group{{ $errors->has('country') ? ' has-danger' : '' }}">
                                <label for="country" class="form-control-label d-block">Country <i class="fa fa-eye text-muted"></i></label>
                                <select id="country" name="country" class="custom-select w-100">
                                    <optgroup>
                                        @foreach (collect(\App\Country::enum())->slice(0, 4) as $code)
                                            <option value="{{ $code }}" {{ old('country') == $code ? 'selected' : '' }}>{{ \App\Country::getString($code) }}</option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup>
                                        @foreach (collect(\App\Country::enum())->slice(4) as $code)
                                            <option value="{{ $code }}" {{ old('country') == $code ? 'selected' : '' }}>{{ \App\Country::getString($code) }}</option>
                                        @endforeach
                                    </optgroup>
                                </select>

                                @if ($errors->has('country'))
                                    <div class="form-control-feedback">
                                        {{ $errors->first('country') }}
                                    </div>
                                @endif
                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="form-group{{ $errors->has('username') ? ' has-danger' : '' }}">
                                <label for="username" class="form-control-label">Desired username <i class="fa fa-eye text-muted"></i></label>
                                <input id="username"
                                       type="text"
                                       class="form-control{{ $errors->has('username') ? ' form-control-danger' : '' }}"
                                       name="username"
                                       value="{{ old('username') }}"
                                       required
                                       autocomplete="off">

                                @if ($errors->has('username'))
                                    <div class="form-control-feedback">
                                        {{ $errors->first('username') }}
                                    </div>
                                @endif
                            </div>

                            <div class="form-group{{ $errors->has('password') ? ' has-danger' : '' }}">
                                <label for="password" class="form-control-label">Password</label>
                                <input id="password"
                                       type="password"
                                       class="form-control{{ $errors->has('password') ? ' has-danger' : '' }}"
                                       name="password"
                                       required>

                                @if ($errors->has('password'))
                                    <span class="form-control-feedback">
                                            {{ $errors->first('password') }}
                                        </span>
                                @endif
                            </div>

                            <div class="form-group">
                                <label for="password-confirm" class="form-control-label">
                                    Confirm Password
                                </label>
                                <input id="password-confirm"
                                       type="password"
                                       class="form-control"
                                       name="password_confirmation"
                                       required>
                            </div>

                            <div class="form-group{{ $errors->has('presentation') ? ' has-danger' : '' }}">
                                <label for="presentation" class="form-control-label">Presentation <i class="fa fa-eye text-muted"></i></label>
                                <textarea id="presentation"
                                          class="form-control{{ $errors->has('presentation') ? ' has-danger' : '' }}"
                                          name="presentation"
                                          rows="5"
                                          required>{{ old('presentation') }}</textarea>

                                @if ($errors->has('presentation'))
                                    <span class="form-control-feedback">
                                                {{ $errors->first('presentation') }}
                                            </span>
                                @endif
                            </div>

                            <div class="form-group{{ $errors->has('media') ? ' has-danger' : '' }}">
                                <label class="form-control-label">Picture/Video</label>
                                <input type="file"
                                       id="media"
                                       name="media[][file]"
                                       class="form-control media-upload"
                                       multiple
                                       required
                                       accept="image/*,video/mp4,video/x-m4v,video/*">

                                @if ($errors->has('media'))
                                    <div class="form-control-feedback">
                                        {{ $errors->first('media') }}
                                    </div>
                                @endif

                                @if ($errors->has('media.0.file'))
                                    <div class="form-control-feedback">
                                        The files must be images or videos and cannot be more than 1G in size.
                                    </div>
                                @endif
                            </div>

                        </div>

                    </div>

                    <div class="form-group{{ $errors->has('terms') ? ' has-danger' : '' }}">
                        <div class="checkbox">
                            <label class="custom-control custom-checkbox">
                                <input type="checkbox"
                                       name="terms"
                                       class="custom-control-input"{{ old('terms') === 'on' ? ' checked' : '' }}>
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">
                                I agree on
                                <a href="{{ route('models.terms') }}">terms</a>
                            </span>

                                @if ($errors->has('terms'))
                                    <div class="form-control-feedback">
                                        {{ $errors->first('terms') }}
                                    </div>
                                @endif
                            </label>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <button type="submit" class="btn btn-primary">Apply</button>
                    </div>

                    <div class="row media-previews pt-1 m-1 rounded"></div>

                </form>
            </div>

            <div class="col-md-4 text-muted" style="font-size:12px">
                <p>
                    Become a model and blogger at Beauties from Heaven today.  Besides being a blog-platform we are also
                    a model agency.  You will have the opportunity to be photographed as "Babe of the month", join other
                    photographers at photoshoots, travelling, partying and also join our nightclub events among other!
                    You will make money and have fun, so join us today!
                </p>
                <p>
                    To apply to be a model/blogger at beautiesfromheaven.com you need to be 18 years old, and be aware
                    of our rules regarding posting of picture/videomateriale. Frontal nudity, topless, bikini, lingerie,
                    booty, nakes and sexy pictures/videos are allowed. Pictures/videos with porn where intercourse,
                    masturbation and use of sextoys is exposed, are not allowed and posts will deleted with no further
                    notice from our Administrator. You only are allowed to post material owned by you or where the right
                    to someone´s work has been given you. Creating of fake profile and misuse of pictures from others
                    will not be tolerated. You will have to follow EU and US laws. It is not allowed for models to give
                    out personal information in blogposts, comments, pictures, videos or giving out information
                    regarding your snapchat, instagram, facebook account and other social media information that will
                    help identifying yourself.
                </p>
            </div>

        </div>
    </div>

    <div id="progress-modal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="text-xs-center">Uploading video&hellip;</div>
                    <progress class="progress progress-success" value="0" max="100"></progress>
                </div>
            </div>
        </div>
    </div>
@endsection
