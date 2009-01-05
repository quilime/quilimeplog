// javascript 1.2
// uses jquery-1.2.6.min.js

$(document).ready(function() 
{
    window.onresize = onResize;
    
    // form submit
    $("#edit_form").submit(function() { 
        submit_edit_form($("#item_type").val());
    });
    
    // realtime content editing
    $('#edit_content').keyup(function(e) {
        $('#content').html( $('#edit_content').val() );
    });
    
    // refresh url after update if the slug is edited
    $('#edit_slug').keyup(function(e) {
        urlRefresh = true;
    });
});


function onResize()
{
    $('#content_textareas textarea').css({height : $(window).height() - 240 }, 250);
}


var urlRefresh = false;

// edit form tab switching
function tab(type)
{
    $('#content_tabs li a').removeClass('selected');
    $('#content_textareas textarea').hide();
    $('#edit_misc').hide();
    $('#tab_'  + type).addClass('selected');
    $('#tab_'  + type).blur();
    $('#edit_' + type).show();
}

function submit_edit_form(type)
{
    var id      = $('#edit_id').val();
    var title   = $('#edit_title').val();
    var slug    = $('#edit_slug').val();
    var hidden  = $('#edit_hidden')[0].checked;
    var content = $('#edit_content').val();
    var tags    = $('#edit_tags').val();    
    var css     = $('#edit_css').val();
    var script  = $('#edit_script').val();
    
    $.ajax({
        type : "POST",
        url  : base_dir + "/edit.php",
        data : {  action  : 'update', 
                  type    : type,
                  id      : id,
                  title   : title,
                  slug    : slug,
                  hidden  : hidden,
                  content : content,
                  tags    : tags,
                  css     : css,
                  script  : script
                },
        success: function()
        {
            show_status(type + ' updated!');            
            if(urlRefresh) {
                if(type == 'project') window.location = base_dir + '/projects/' + slug + '/';
                else if(type == 'project') window.location = base_dir + '/posts/' + slug + '/';    
            }                
            return false;
        }                    
    });    
    return false;
}

function show_status(str)
{
    $('#status').text(str).fadeIn(10).fadeTo(2000, 1).fadeOut(1000);
}

function edit_form_success()
{
    console.log('edit success!');
}


function insert_post(project_slug)
{
    $.ajax({
        type : "POST",
        url  : base_dir + "/edit.php",
        data : { action       : 'insert', 
                 type         : 'post',
                 project_slug : escape(project_slug)
               },
        success : function(msg) 
        {
            $.getJSON(base_dir + "/posts/?format=json&count=1&order-by=newest",
                function(data) { 
					window.location = data.posts[0].url;
            });
        }
    });
}


function insert_project()
{
    $.ajax({
        type : "POST",
        url  : base_dir + "/edit.php",
        data : { action : 'insert', 
                 type   : 'project'
               }, 
        success: function(msg) 
        {
            $.getJSON(base_dir + "/projects/?format=json&count=1&order-by=newest",
                function(data) { 
                    window.location = data.projects[0].url;
            });
        }
    });
}


function delete_item(event)
{
    var type = $('#item_type').val();
    var id   = $('#edit_id').val();    

    if(confirm('delete?')) {
        $.ajax({
            type: "POST",
            url: base_dir + "/edit.php",
            data: { action : 'delete',
                    type   : type,
                    id     : id
                  },
            success: function()
            {
                if(type == 'project')
                    window.location = base_dir + '/projects/';
                if(type == 'post')
                    window.location = base_dir + '/posts/';
            }                    
        });
    }
}


function toggle_edit_panel()
{
    if($("#edit_panel").css('display') == 'block') {
        $("#edit_panel").hide(400);
        $("#toggle, #edit_panel").animate({right : 0}, 400, function() {
            $("#toggle").html("&larr");
        });
    } 
    else {
        $("#edit_panel").show(400);
        $("#toggle").animate({right : 520}, 400, function() {
            $("#toggle").html("&rarr");
        });    
    }
}
























function save_project_list_order(event) 
{
    var list = $('#project_list').sortable('serialize');
    update_setting('project_list_order', list)
}


function update_setting(setting_title, value)
{
    $('#updating').show();
    $.ajax({
        type: "POST",
        url: base_dir + "/edit.php",
        data: 'action=update' + 
              '&type=setting'+ 
              '&title=' + setting_title +
              '&value=' + escape(value),
        success: function() {
            $('#updating').hide();
        }                    
    });
}


function insert_spacer(event)
{
    var id = $('#project_list li.spacer').length + 1;

    var spacer = $('<li id="spacer_'+id+'" class="spacer"><div class="handle"></div>&nbsp;</li>');
    spacer.hide();
    $('#project_list').prepend(spacer);
    spacer.fadeIn(500);
}



function edit_section_title(elem, section_title, section_id)
{
    var li = $(elem).parent();
    var update = $('<input type="button" value="update">').bind('click', function() { 
            console.log($('#section_title_'+section_id)); 
        });    
    li.empty().append('<div class="handle"></div><input id="section_title_'+section_id+'" type="text" value="' + section_title + '">');
    li.append(update);
}   



function media_browser(event)
{
    window.open( base_dir + '/browser.php', 
                 'media browser', 
                 'menubar=no,width=430,height=360,toolbar=no'
                );
}





function edit_title(event)
{
    var button = event.currentTarget;
    $(button).unbind('click', edit_title);
    
    $('#title_content').show();
    
    $.getJSON("?format=json",   
    function(data) 
    {
        var item = {};
        if(data.projects)    item = data.projects[0];
        else if (data.posts) item = data.post[0];            
        $(button).bind('click', function() 
        {
            var title  = $('#edit_title');
            var slug   = $('#edit_slug');
            var hidden = $('#edit_hidden');
            $.ajax({
                type: "POST",
                url: base_dir + "/edit.php",
                data: 'action=update' + 
                      '&type=' + type + 
                      '&id=' + item.id + 
                      '&title=' + escape($('#edit_title').val()) + 
                      '&slug=' + escape($('#edit_slug').val()) + 
                      '&hidden=' + escape($('#edit_hidden')[0].checked),
                success: function()
                {
                    if(type == 'project')
                        window.location = base_dir + '/projects/' + $('#edit_slug').val() + '/';
                }                    
            });
        });
    });
}


function edit_content(event)
{
    var button = event.currentTarget;
    button.value = 'update';
    $(button).unbind('click', edit_content);

    $('#content').hide();
    $('#content_textarea').show(); 
    $('#media_browser').bind('click',  media_browser);

    $.getJSON("?format=json",   
    function(data) 
    {
        var item = {};
        if(data.projects)    item = data.projects[0];
        else if (data.posts) item = data.post[0];
        $(button).bind('click', function() 
        {
            $.ajax({
                type: "POST",
                url: base_dir + "/edit.php",
                data: 'action=update' + 
                      '&type=' + type + 
                      '&id=' + item.id + 
                      '&content=' + escape($('#content_textarea').val()),
                success: function()
                {
                    $.getJSON("?format=json",
                    function(data) {
                        if(data.projects)    item = data.projects[0];
                        else if (data.posts) item = data.post[0];
                        $('#content').empty().html(item.content_cleaned).show();
                        $('#content_textarea').hide();
                        $('#media_browser').hide().unbind('click',  media_browser);
                        $(button).unbind().bind('click', edit_content);
                        button.value = 'edit';
                    });
                }
            });
        });
    });
}





function insert_section(event)
{
    $.ajax({
        type: "POST",
        url: base_dir + "/edit.php",
        data: "action=insert&type=section",
        success: function(msg) 
        {
            $.getJSON(base_dir + "/sections/?format=json&count=1&order-by=newest",
                function(data) { 
                    $.each(data.sections, function(i, section) {
                        var li = $('<li id="section_' + section.id 
                        + '" class="section"><div class="handle"></div>' 
                        + section.title + '</li>');
                        $('#project_list').prepend(li);
                        li.fadeIn(500);
                    });
            });
        }
    });
}




