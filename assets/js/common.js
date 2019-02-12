//Fluid Tabs
var navPillsAdjust = function () {
    $('.nav.nav-tabs').each(function () {
        var liGroup = $(this).children('li');
        //console.log( liGroup.length );
        var liLength = liGroup.length;
        liGroup.each(function () {
            var liWidth = 100 / liLength - 1;
            $(this).css({'min-width': liWidth + '%', 'margin-left': '0px', 'margin-right': '0px'});
        });
    });
};
navPillsAdjust();
// Multiple menu switcher
var submit = $('input[type="submit"]');
var tabs = $('#tabs');
var parkList = $('#parkList');
submit.click(function (e) {
    parkList.toggle();
});
//Sidebar Toggle
$('.db-sidebar-toggle').click(function () {
    $('.db-sidebar').toggleClass('db-collapsed');
    $('.db-nav').toggleClass('db-sidebar-collapsed-pad');
    $('.db-content').toggleClass('db-sidebar-collapsed-pad');
});
$(window).bind("resize", function () {
// console.log($(this).width())
    if ($(this).width() < 767) {
        $('.db-sidebar').addClass('db-collapsed');
        $('.db-nav').addClass('db-sidebar-collapsed-pad');
        $('.db-content').addClass('db-sidebar-collapsed-pad');
    } else {
        $('.db-sidebar').removeClass('db-collapsed');
        $('.db-nav').removeClass('db-sidebar-collapsed-pad');
        $('.db-content').removeClass('db-sidebar-collapsed-pad');
    }
}).trigger('resize');
//On select option change content
$(document).on('change', '.div-toggle', function () {
    var target = $(this).data('target');
    var show = $("option:selected", this).data('show');
    $(target).children().addClass('hide');
    $(show).removeClass('hide');
});
$(document).ready(function () {
    $('.div-toggle').trigger('change');
});

function set_val(field, value) {
    if (value != undefined && value != null && value != "NULL") {
        if ($(field).val() == "")
            $(field).val(value);
    }
}

function make_number(rough_number) {
    real_number = rough_number.replace(/-/g, "");
    real_number = real_number.replace(/ /g, "");
    return real_number;
}


function set_missing_status(status) {
    if (status == "Missing item requested") {
        return '<span class="fc-event-dot" style="background-color:#f74444"></span>  ' + status;
    } else if (status == "Items uploaded for review") {
        return '<span class="fc-event-dot" style="background-color:#88b794"></span>  ' + status;
    } else {
        return status;
    }
}

