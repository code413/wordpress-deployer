require('./bootstrap');

require('jquery-confirm');

$(document).ready(function () {
    $('.replacement-type').change(function (e) {
        const $fileSelection = $('.file-selection');

        if(e.target.value === 'Database'){
            $fileSelection.attr('hidden', true);
            return;
        }

        $fileSelection.removeAttr('hidden');
    });

    $('.confirmation-alert').confirm({
        title: 'Delete!',
        content: 'Are you sure you want to delete this item?',
        type: 'red',
        typeAnimated: true,
        buttons: {
            confirm: {
                text: 'Delete',
                btnClass: 'btn-red',
                action(){
                    location.href = this.$target.attr('href');
                }
            },
            cancel(){
            }
        }
    });
})
