var $ = jQuery.noConflict();
$(document).ready(function(){

    // Uploading files
    var file_frame,
        edit_frame,
        template = $('#template').html(),
        initSingleBtns = function () {

            $('#image-list').on('click', '.dashicons-edit', function() {
                var imageID = $(this).data('id');

                edit_frame = wp.media.frames.edit_frame = wp.media(
                    {
                        title: 'Edit Image Details',
                        id: 'edit-frame-id',
                        button: {
                            text: 'Save',
                        },
                        multiple: false,
                        library:  {type: 'image'}
                    }
                );


                edit_frame.on('open',function() {

                    wp.media.attachment(imageID).fetch();

                    //set selection
                    var selection   =   edit_frame.state().get('selection'),
                        attachment  =   wp.media.attachment( imageID );

                    attachment.fetch();
                    selection.add( attachment );

                });


                //edit_frame.content.mode('browse');
                edit_frame.open();

            });

            $('#image-list').on('click', '.dashicons-dismiss', function(){
                if (confirm('Are you sure?')) {
                    $(this).parent().remove();
                }
            });
        }

    Mustache.parse(template);   // optional, speeds up future uses
    if (typeof galleryItems !== "undefined") {
        var rendered = Mustache.render(template, galleryItems);
        $('#image-list').html(rendered);
        $('#target').hide();
    }

    initSingleBtns();

    $('#feat_media_button').on('click', function( event ){

        event.preventDefault();
        if ( file_frame ) {
            file_frame.uploader.uploader.param( 'post_id', etlGalleryData.pid );
            file_frame.open();
            return;
        }

        file_frame = wp.media.frames.file_frame = wp.media(
            {
                title: $( this ).data( 'box-title' ),
                button: {
                    text: $( this ).data( 'btn-text' ),
                },
                library:  {type: 'image'},
                multiple: true
            }
        );

        file_frame.on( 'select', function() {
            attachment = file_frame.state().get('selection').toJSON();

            console.log(attachment);

            var selectedItems = {
                items: []
            };

            // Do something with attachment.id and/or attachment.url here
            $.each(attachment, function( index, value ) {
                etlGalleryData.count++;

                if (this.type === 'image') {
                    selectedItems.items.push({
                        i: etlGalleryData.count,
                        id: this.id,
                        url: this.sizes.full.url,
                        title: this.title,
                        caption: this.caption,
                        alt: this.alt
                    });
                }

            });

            $('#image-list').append(Mustache.render(template, selectedItems));
        });

        file_frame.open();
    });

    $('#remove_all').on('click', function(e){
        e.preventDefault();
        if (confirm('Are you sure?')) {
            $('#image-list').children().remove();

        }
    });

    $('#image-list').sortable();
    $('#image-list').disableSelection();

    /**
    * gallery select
    */
    $("#gallery-select-sc").on('change', function() {
        var currVal = $("#gallery-select-sc :selected").val();
        send_to_editor(currVal);
        return false;
    });


});
