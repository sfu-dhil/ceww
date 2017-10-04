(function ($, window) {

    function windowBeforeUnload(e) {
        var clean = true;
        $('form').each(function () {
            var $form = $(this);
            if ($form.data('dirty')) {
                clean = false;
            }
        });
        if (!clean) {
            var message = 'You have unsaved changes.';
            e.returnValue = message;
            return message;
        }
    }
    
    function formDirty($form) {
        var $form = $(this);
        $form.data('dirty', false);
        $form.on('change', function () {
            $form.data('dirty', true);
        });
        $form.on('submit', function () {
            $(window).unbind('beforeunload');
        });
    }
    
    function formPopup(e) {
        e.preventDefault();
        var url = $(this).prop('href');
        window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,top=60,left=60,width=500,height=600");
    }

    function addCollectionItem($container) {
        var prototype = $container.data('prototype');
        var index = $container.data('count');
        var $form = $(prototype.replace(/__name__/g, index).replace(/label__/g, ''));
        $container.append($form);
        $form.children('label').replaceWith('<br/><div class="col-sm-2"><a class="btn btn-primary remove"><span class="glyphicon glyphicon-minus"></span> Remove</a></div>');
        $form.find("a.remove").click(function (e) {
            e.preventDefault();
            $form.remove();
        });
        $form.find('.select2entity[data-autostart="true"]').select2entity();
        $container.data('count', index + 1);
    }
    
    function setupCollection(idx, element) {
        var $e = $(element);
        $e.children("label").append('<br/><a href="#" class="btn btn-primary add"><span class="glyphicon glyphicon-plus"></span> Add</a>');
        var $a = $e.find("a");
        var $container = $e.find('div[data-prototype]');
        $container.data('count', $container.find('div.form-group').length);
        $a.click(function (e) {
            e.preventDefault();
            addCollectionItem($container);
        });
        $e.find('div[data-prototype]').children('div').children('label').replaceWith('<br/><div class="col-sm-2"><a class="btn btn-primary remove"><span class="glyphicon glyphicon-minus"></span> Remove</a></div>');
        $e.find("a.remove").click(function (e) {
            e.preventDefault();
            $(this).closest('div.form-group').remove();
        });
    }

    $(document).ready(function () {
        $(window).bind('beforeunload', windowBeforeUnload);
        $('form').each(formDirty);
        $("a.popup-form").click(formPopup);
        $('form div.collection').each(setupCollection);
    });

    function addCollectionItem($container) {
        var prototype = $container.data('prototype');
        console.log(prototype);
        var index = $container.data('count');
        var $form = $(prototype.replace(/__name__/g, index).replace(/label__/g, ''));
        $container.append($form);
        $form.children('label').replaceWith('<br/><div class="col-sm-2"><a class="btn btn-primary remove"><span class="glyphicon glyphicon-minus"></span> Remove</a></div>');
        $form.find("a.remove").click(function(e){
            e.preventDefault();
            $form.remove();
        });
        $container.data('count', index + 1);
    }

    $(document).ready(function(){
        console.log('hi hi.');
        $('form div.collection').each(function(idx, element){
            var $e = $(element);
            $e.children("label").append('<br/><a href="#" class="btn btn-primary add"><span class="glyphicon glyphicon-plus"></span> Add</a>');
            var $a = $e.find("a");
            var $container = $e.find('div[data-prototype]');
            $container.data('count', $container.find('div.form-group').length);
            $a.click(function(e){
                e.preventDefault();
                addCollectionItem($container);
            });
        });
    });

})(jQuery, window);
