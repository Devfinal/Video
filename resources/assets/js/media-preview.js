var $input = $('.media-upload');
const $mediaPreviews = $('.media-previews');
const $mainContent = $('.main-content');

// @note Fix iOS "-webkit-overflow-scrolling: touch" rendering bug by scrolling up and down
//       1px to force a rerender of the checkbox.
$mediaPreviews.on('click', '.checkbox', event => {
    if (event.target.tagName.toLowerCase() === 'input') {
        $mainContent.scrollTop($mainContent.scrollTop() + 1);
        $mainContent.scrollTop($mainContent.scrollTop() - 1);
    }
});

$input.on('change', event => {
    $mediaPreviews.removeClass('bordered').html('');
    $input.closest('.form-group').removeClass('has-danger').find('.upload-error').remove();

    if (event.currentTarget.files.length > 0) {
        $mediaPreviews.addClass('bordered').html('<div class="loader"><i class="fa fa-refresh fa-spin"></i></div>');
        $mediaPreviews.css('min-height', '4em');

        async.eachOfSeries(event.currentTarget.files, (file, index, done) => {
            const fileReader = new FileReader;

            if (file.type.match('image')) {
                fileReader.addEventListener('loadend', event => {
                    $mediaPreviews.append(
                        '<div class="col-xs-6 col-lg-3">' +
                            '<div class="card">' +
                                '<div style="position:relative">' +
                                    '<img src="' + fileReader.result + '" class="card-img-top img-fluid">' +
                                '</div>' +
                                '<div class="card-block">' +
                                    '<div class="checkbox float-xs-left">' +
                                        '<label class="custom-control custom-checkbox mb-0">' +
                                            '<input type="checkbox" name="media[' + index + '][protected]" class="custom-control-input">' +
                                            '<span class="custom-control-indicator"></span>' +
                                            '<span class="custom-control-description">VIP</span>' +
                                        '</label>' +
                                    '</div>' +
                                '</div>' +
                            '</div>' +
                        '</div>'
                    );

                    done();
                }, { once: true });

                fileReader.readAsDataURL(file);
            } else if (file.type.match('video')) {
                // @note We use a static placeholder image for all video files as generating a preview of large (+200MB)
                //       has proven to crash the browser. That may be due to base64 limits.
                $mediaPreviews.append(
                    '<div class="col-xs-6 col-lg-3">' +
                        '<div class="card">' +
                            '<div style="position:relative">' +
                                '<img src="https://beautiesfromheaven.com/images/placeholder-video.png" class="card-img-top img-fluid">' +
                                '<i class="fa fa-play-circle-o fa-4x play-icon"></i>' +
                                '<input type="hidden" name="media-video-thumbnail[' + index + ']" value="https://beautiesfromheaven.com/images/placeholder-video.png">' +
                            '</div>' +
                            '<div class="card-block">' +
                                '<div class="checkbox float-xs-left">' +
                                    '<label class="custom-control custom-checkbox mb-0">' +
                                        '<input type="checkbox" name="media[' + index + '][protected]" class="custom-control-input">' +
                                        '<span class="custom-control-indicator"></span>' +
                                        '<span class="custom-control-description">VIP</span>' +
                                    '</label>' +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                    '</div>'
                );

                // @note Just to compensate for image load time.
                setTimeout(() => {
                    done();
                }, 250);
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
