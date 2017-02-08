const $form = $('#save-post');
const $input = $('#media');
const $modal = $('#progress-modal');
const $progressbar = $modal.find('.progress');

$form.on('submit', event => {
    var containsVideo = false;
    const fileList = $input.get(0).files;

    for (let i = 0, l = fileList.length; i < l; i++) {
        if (fileList.item(i).type.match('video')) {
            containsVideo = true;

            break;
        }
    }

    if (containsVideo) {
        let xhr = new XMLHttpRequest;

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

        // make sure the tinyMCE content is dumped before creating the payload
        $form.find('#content').html(tinymce.get('content').getContent());

        xhr.open(event.currentTarget.getAttribute('method'), event.currentTarget.getAttribute('action'), true);
        xhr.send(new FormData(event.currentTarget));
    }
});
