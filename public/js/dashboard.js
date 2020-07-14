$(".selectpicker").selectpicker(); //bootstrap selectpicker initialized
/*******************************************
******* Start :: Show Modal Function *******
********************************************/
function show_modal(modal_title,btn_text){
    $('#saveDataForm')[0].reset(); //reset form
    $('#update_id').val('');//empty id input field
    
    $(".error").each(function () {
        $(this).empty(); //remove error text
    });
    $("#saveDataForm").find('.is-invalid').removeClass('is-invalid'); //remover red border color

    $('#saveDataModal').modal({
        keyboard: false,
        backdrop: 'static', //make modal static
    });
    
    $('.selectpicker').selectpicker('refresh');//empty selectpicker field
    $('.modal-title').html('<i class="fas fa-plus-square"></i> <span>'+modal_title+'</span>'); //set modal title
    $('#save-btn').text(btn_text); //set save button text
}
/*****************************************
******* End :: Show Modal Function *******
******************************************/

/***********************************************************
******* Start :: On Checked Select All Rows Of Table *******
************************************************************/
function select_all(){
    if($('.selectall:checked').length == 1){
        $('.select_data').prop('checked',$(this).prop('checked',true)); 
        if($('.select_data').is(':checked'))
        {
            $('.select_data').closest('tr').addClass('bg-danger');
            $('.select_data').closest('tr').children('td').addClass('text-white');
        }
        else
        {
            $('.select_data').closest('tr').removeClass('bg-danger');
            $('.select_data').closest('tr').children('td').removeClass('text-white');
        }
    }else{
        $('.select_data').prop('checked',false);
        if($('.select_data').is(':checked'))
        {
            $('.select_data').closest('tr').addClass('bg-danger');
            $('.select_data').closest('tr').children('td').addClass('text-white');
        }
        else
        {
            $('.select_data').closest('tr').removeClass('bg-danger');
            $('.select_data').closest('tr').children('td').removeClass('text-white');
        }
    }
}
/***********************************************************
******* End :: On Checked Select All Rows Of Table *******
************************************************************/

/*****************************************************************
    ******* Start :: Prevent Form Submissin On Press Enter Key *******
    ******************************************************************/
   $(document).ready(function() {
    $(window).keydown(function(event){
        if(event.keyCode == 13) {
        event.preventDefault();
        return false;
        }
    });
});
/*****************************************************************
******* End :: Prevent Form Submissin On Press Enter Key *******
******************************************************************/

/*****************************************
******* Start :: Windows Preloader *******
******************************************/
function handlePreloader() {
    if($('#preloader').length){
        $('#preloader').delay(220).fadeOut(500);
    }
}
$(window).on('load', function() {
    handlePreloader();
});
/*****************************************
******* End :: Windows Preloader *******
******************************************/

/**************************************************
******* Start :: Bootstrap Notify Functions *******
**************************************************/
   
function bootstrap_notify(type,message){
    $.notify({
        message: message
    },{
            type: type,
            allow_dismiss: true,
            newest_on_top: true,
            mouse_over: null,
            showProgressbar: false,
            spacing: 10,
            timer: 1000,
            placement: {
                from: "bottom",
                align: "right"
            },
            offset: 20,
            delay: 5000,
            z_index: 10000,
            animate: {
                enter: 'animated fadeInRight',
                exit: 'animated fadeOutRight'
            },

            template: '' +
                '<div data-notify="container" class="alert alert-{0} m-alert" role="alert">' +
                '<button type="button" aria-hidden="true" class="close" data-notify="dismiss"></button>' +
                '<span data-notify="icon"></span>' +
                '<span data-notify="title">{1}</span>' +
                '<span data-notify="message">{2}</span>' +
                '<div class="progress" data-notify="progressbar">' +
                '<div class="progress-bar progress-bar-animated bg-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>' +
                '</div>' +
                '<a href="{3}" target="{4}" data-notify="url"></a>' +
                '</div>'
        }
    );
}

/**************************************************
 ******* End :: Bootstrap Notify Functions *******
**************************************************/

/*************************************
******* Start :: URL Generator *******
**************************************/
function url_generator(input_value,output_id){
    var value = input_value.toLowerCase();
    var str = value.replace(/ +(?= )/g,'');
    var name =str.split(' ').join('-')
    $("#"+output_id).val(name);
}
/*************************************
******* End :: URL Generator *******
**************************************/

/*************************************
******* Start :: Permission Tree *******
**************************************/
$.fn.extend({
    treed: function (o) {
      
      var openedClass = 'fa-minus-square';
      var closedClass = 'fa-plus-square';
      
      if (typeof o != 'undefined'){
        if (typeof o.openedClass != 'undefined'){
        openedClass = o.openedClass;
        }
        if (typeof o.closedClass != 'undefined'){
        closedClass = o.closedClass;
        }
      };
      
        //initialize each of the top levels
        var tree = $(this);
        tree.addClass("tree");
        tree.find('li').has("ul li").each(function () {
            var branch = $(this); //li with children ul
            branch.prepend("<i class='indicator fas " + closedClass + "'></i>");
            branch.addClass('branch');
            branch.on('click', function (e) {
                if (this == e.target) {
                    var icon = $(this).children('i:first');
                    icon.toggleClass(openedClass + " " + closedClass);
                    $(this).children().children().slideToggle(500);
                }
            })
            branch.children().children().slideToggle(500);
        });
        //fire event from the dynamically added icon
      tree.find('.branch .indicator').each(function(){
        $(this).on('click', function () {
            $(this).closest('li').click();
        });
      });
        //fire event to open branch if the li contains an anchor instead of text
        tree.find('.branch>a').each(function () {
            $(this).on('click', function (e) {
                $(this).closest('li a').click();
                e.preventDefault();
            });
        });
        //fire event to open branch if the li contains a button instead of text
        tree.find('.branch>button').each(function () {
            $(this).on('click', function (e) {
                $(this).closest('li a').click();
                e.preventDefault();
            });
        });
    }
});

// $('#permission').treed();
// $('#treeList').treed();
/*************************************
******* End :: Permission Tree *******
**************************************/

