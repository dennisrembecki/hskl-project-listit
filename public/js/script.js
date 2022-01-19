/*function test() {


    $.ajax({
        url: 'https://id.twitch.tv/oauth2/token?client_id=5o034mh4jtkv1t2w8rzbzu3vb94n60&client_secret=18yk6hcyqtjhg80ya7d8f3zte51ysy&grant_type=client_credentials',
        type: 'POST',
        dataType: 'json',
        data: {},
        success: function (data) {
            var token = data["access_token"];
            $.ajax({
                url: 'https://api.igdb.com/v4/games',
                type: 'POST',
                headers: {"Client-ID": "5o034mh4jtkv1t2w8rzbzu3vb94n60", "Authorization": "Bearer " + token},
                dataType: 'json',
                success: function (data) {
                    console.log(data);
                }
            });

            $.ajax({
                method: "GET",
                headers: {
                    "user-key": userKey,
                    Accept: contentType
                },
                url:
                    "https://cors-anywhere.herokuapp.com/https://api-endpoint.igdb.com/games/?fields=id,name,summary,cover.url,platforms,first_release_date,popularity&order=popularity:desc&limit=50",
                async: true,
                crossDomain: true
            })
                .done(function (response) {
                    // Save the games
                    games = response;

                    // Populate the games search list
                    $.each(response, function (index, item) {
                        igdbGameList.append(buildGameItem(item));
                    });
                })
                .fail(function () {
                    alert("Unable to contact IGDB. Be sure to paste your API user key in the userKey variable at the top of the JS.");
                });


        }
    });
}
*/
$(function () {

        // BOOTSTRAP
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })

        // AJAX VARIABLES
        var ajax_login = null;
        var ajax_vote_up = null;
        var ajax_vote_up_remove = null;
        var ajax_vote_down = null;
        var ajax_vote_down_remove = null;
        var ajax_vote_star = null;
        var ajax_removevote_star = null;
        var ajax_changedate = null;
        var ajax_delete = null;
        var ajax_favorite = null;

        // NAVBAR HAMBURGER
        var myCollapsible = document.getElementById('navbarScroll')
        myCollapsible.addEventListener('hide.bs.collapse', function () {
            $(".hamburger").removeClass('is-active');
        });
        myCollapsible.addEventListener('show.bs.collapse', function () {
            $(".hamburger").addClass('is-active');
        });

        // NAVBAR SEARCH
        $("#search").submit(function (e) {
            e.preventDefault();
            e.stopPropagation();

            let keyword = $("#search_input_nav").val();
            if (keyword) {
                window.location.href = '/search/' + $("#search_input_nav").val();
            }
        });

        // LOGIN
        if ($("#login-button").length) {

            // REFRESH ON BACK
            window.addEventListener("pageshow", function (event) {
                var historyTraversal = event.persisted || (typeof window.performance != "undefined" && window.performance.navigation.type === 2);
                if (historyTraversal) {
                    window.location.reload();
                }
            });

            var login_button = $("#login-button");
            var real_login_button = $("#real-login-button");

            $('.input').keypress(function (e) {
                if (e.which == 13) {
                    if (!real_login_button.hasClass("d-none")) {
                        real_login_button.click();
                    } else {
                        login_button.click();
                    }
                    return false;
                }
            });

            login_button.click(function () {

                var form = document.getElementById("login");
                var email = $("#email").val();

                if (form.checkValidity()) {

                    login_button.html('<i class="fa-solid fa-cog fa-spin"></i>');
                    login_button.addClass("disabled");

                    if (ajax_login) ajax_login.abort();
                    ajax_login = $.ajax({
                        url: '/ajax/checkmail',
                        type: 'POST',
                        dataType: 'json',
                        data: {email: email},
                        success: function (data) {
                            if (data === "register") {
                                window.location.href = "/register";
                            } else if (data === "login") {
                                $(".login-wrapper").removeClass("d-none");
                                $("#real-login-button").removeClass("d-none");
                                $("#password").prop('required', true);
                                login_button.remove();
                            }
                        }
                    });
                } else {
                    form.reportValidity();
                }

            });

            $("#login").submit(function (e) {
                real_login_button.addClass("disabled").html('<i class="fa-solid fa-cog fa-spin"></i>');
            });

        }

        // REGISTER
        if ($("#register-button").length) {
            var register_button = $("#register-button");
            register_button.click(function () {
                register_button.addClass("disabled").html('<i class="fa-solid fa-cog fa-spin"></i>');
            });
        }

        // VIEW
        if ($("#view").length) {

            // Filter
            $("#filter").click(function() {
               $("#filter-buttons").toggleClass("show");
            });

            // Copy Elements
            $("#button_newlist").click(function () {
                $("#button_newlist").addClass("active");
                $("#button_list").removeClass("active");
                $("#addto_list").removeClass("d-none");
                $("#addto_newlist").addClass("d-none");
                $("#newlist").val("no");
            });
            $("#button_list").click(function () {
                $("#button_newlist").removeClass("active");
                $("#button_list").addClass("active");
                $("#addto_list").addClass("d-none");
                $("#addto_newlist").removeClass("d-none");
                $("#newlist").val("yes");
            });
            $(".copy").click(function () {
                var id = $(this).attr("data-id");
                var name = $(this).attr("data-name");
                $("#copy_id").val(id);
                $("#copy_name").html(name);
            });

            // Change Date
            $(".input-date").change(function (e) {
                $date = $(this).val();
                $id = $(this).attr("data-id");
                if (ajax_changedate) ajax_changedate.abort();
                ajax_changedate = $.ajax({
                    url: '/ajax/modifydate',
                    type: 'POST',
                    dataType: 'json',
                    data: {date: $date, id: $id},
                    success: function (data) {

                    }
                });
            });

            // Trailer beim anklicken Anzeigen
            var trailerModal = document.getElementById('showTrailer');
            trailerModal.addEventListener('show.bs.modal', function (e) {
                if ($(e.relatedTarget).hasClass("hover-title")) {
                    e.stopPropagation();
                    e.preventDefault();
                    return false;
                }

                showTrailer(e.relatedTarget.getAttribute('data-name'));
            })
            trailerModal.addEventListener('hide.bs.modal', function (e) {
                $("#trailer").html("<i class=\"fa-solid fa-spinner fa-spin\"></i>");
            })

            // Cover beim Erstellen eines Elements vorschlagen
            var timeout = null;
            $("#loadCover").keyup(function () {
                clearTimeout(timeout);
                var movie_name = $(this).val();
                timeout = setTimeout(function () {
                    getCover(movie_name, $("#covermode").val());
                }, 500);
            });
            $(document).on("click", ".cover", function (e) {
                $(".cover").removeClass("selected");
                $(this).addClass("selected");
                $(".selected-icon").remove();
                $(this).parent().append("<div class='selected-icon position-absolute' style='font-size: 34px;opacity:0.8;top:0;right:9px;'><i class=\"fa-regular fa-circle-check\"></i></div>")
                $("#coverurl").val($(this).attr("src"));

                if ($(this).attr("data-name") != undefined)
                    $("#loadCover").val(decodeURI($(this).attr("data-name")));

                if ($(this).attr("data-rel") != undefined)
                    $("#date").val(decodeURI($(this).attr("data-rel")));

                $("#loadCover").focus();
            });

            //Bewertung: 6 Sterne
            $(".stars").each(function () {
                refreshStars($(this), $(this).attr("data-value"));
            });
            var stars_parent, stars;
            $(".stars-wrapper").on('click', '.star', function (e) {

                var star_index = $(this).index();
                var stars = $(this);
                var parent_stars_wrapper = $(this).parent();
                var id = $(this).parent().attr("data-id");
                var stimmen = "Stimmen";

                if (parent_stars_wrapper.attr("data-rated") == (star_index + 1) * 2) {
                    //REMOVE VOTE
                    if (ajax_removevote_star) ajax_removevote_star.abort();
                    ajax_removevote_star = $.ajax({
                        url: '/ajax/element/removevote',
                        type: 'POST',
                        dataType: 'json',
                        data: {id: $(this).parent().attr("data-id"), type: "stars"},
                        success: function (data) {
                            parent_stars_wrapper.removeClass("voted");
                            parent_stars_wrapper.attr("data-rated", "0");
                            parent_stars_wrapper.attr("data-value", data[1]);
                            refreshStars(parent_stars_wrapper, data[1]);
                            if (data[0] === 1) {
                                stimmen = "Stimme";
                            } else {
                                stimmen = "Stimmen";
                            }
                            console.log(parent_stars_wrapper);
                            if (parent_stars_wrapper.hasClass("voted")) {
                                $('.heart-info[data-id="' + id + '"]').addClass("gold");
                            } else {
                                $('.heart-info[data-id="' + id + '"]').removeClass("gold");
                            }
                            $('.heart-info[data-id="' + id + '"]').html(data[0] + " " + stimmen);
                        }
                    });
                } else {
                    if (ajax_vote_star) ajax_vote_star.abort();
                    ajax_vote_star = $.ajax({
                        url: '/ajax/element/vote',
                        type: 'POST',
                        dataType: 'json',
                        data: {id: $(this).parent().attr("data-id"), value: star_index + 1},
                        success: function (data) {
                            parent_stars_wrapper.addClass("voted");
                            parent_stars_wrapper.attr("data-rated", data[1]);
                            refreshStars(parent_stars_wrapper, data[1]);
                            if (data[0] === 1) {
                                stimmen = "Stimme";
                            } else {
                                stimmen = "Stimmen";
                            }
                            if (parent_stars_wrapper.hasClass("voted")) {
                                $('.heart-info[data-id="' + id + '"]').addClass("gold");
                            } else {
                                $('.heart-info[data-id="' + id + '"]').removeClass("gold");
                            }
                            $('.heart-info[data-id="' + id + '"]').html(data[0] + " " + stimmen);
                        }
                    });
                }

                if (star_index == "0" || star_index == 0) {
                    star_index = "2";
                } else if (star_index == "1") {
                    star_index = "4";
                } else if (star_index == "2") {
                    star_index = "6";
                } else if (star_index == "3") {
                    star_index = "8";
                } else if (star_index == "4") {
                    star_index = "10";
                } else if (star_index == "5") {
                    star_index = "12";
                }
                $(this).parent().addClass("voted").attr("data-value", star_index);

            });
            $(".stars-wrapper").on('mouseout', '.star', function (event) {
                if (stars && stars_parent != undefined) {

                    var halfhearts = stars_parent.attr("data-value");

                    if (halfhearts == 0) {
                        stars.eq(0).removeClass().addClass("fa-regular fa-star star");
                        stars.eq(1).removeClass().addClass("fa-regular fa-star star");
                        stars.eq(2).removeClass().addClass("fa-regular fa-star star");
                        stars.eq(3).removeClass().addClass("fa-regular fa-star star");
                        stars.eq(4).removeClass().addClass("fa-regular fa-star star");
                        stars.eq(5).removeClass().addClass("fa-regular fa-star star");
                    } else if (halfhearts == 2) {
                        stars.eq(0).removeClass().addClass("fa-solid fa-star star");
                        stars.eq(1).removeClass().addClass("fa-regular fa-star star");
                        stars.eq(2).removeClass().addClass("fa-regular fa-star star");
                        stars.eq(3).removeClass().addClass("fa-regular fa-star star");
                        stars.eq(4).removeClass().addClass("fa-regular fa-star star");
                        stars.eq(5).removeClass().addClass("fa-regular fa-star star");
                    } else if (halfhearts == 3) {
                        stars.eq(0).removeClass().addClass("fa-solid fa-star star");
                        stars.eq(1).removeClass().addClass("fa-regular fa-star-half-stroke star");
                        stars.eq(2).removeClass().addClass("fa-regular fa-star star");
                        stars.eq(3).removeClass().addClass("fa-regular fa-star star");
                        stars.eq(4).removeClass().addClass("fa-regular fa-star star");
                        stars.eq(5).removeClass().addClass("fa-regular fa-star star");
                    } else if (halfhearts == 4) {
                        stars.eq(0).removeClass().addClass("fa-solid fa-star star");
                        stars.eq(1).removeClass().addClass("fa-solid fa-star star");
                        stars.eq(2).removeClass().addClass("fa-regular fa-star star");
                        stars.eq(3).removeClass().addClass("fa-regular fa-star star");
                        stars.eq(4).removeClass().addClass("fa-regular fa-star star");
                        stars.eq(5).removeClass().addClass("fa-regular fa-star star");
                    } else if (halfhearts == 5) {
                        stars.eq(0).removeClass().addClass("fa-solid fa-star star");
                        stars.eq(1).removeClass().addClass("fa-solid fa-star star");
                        stars.eq(2).removeClass().addClass("fa-regular fa-star-half-stroke star");
                        stars.eq(3).removeClass().addClass("fa-regular fa-star star");
                        stars.eq(4).removeClass().addClass("fa-regular fa-star star");
                        stars.eq(5).removeClass().addClass("fa-regular fa-star star");
                    } else if (halfhearts == 6) {
                        stars.eq(0).removeClass().addClass("fa-solid fa-star star");
                        stars.eq(1).removeClass().addClass("fa-solid fa-star star");
                        stars.eq(2).removeClass().addClass("fa-solid fa-star star");
                        stars.eq(3).removeClass().addClass("fa-regular fa-star star");
                        stars.eq(4).removeClass().addClass("fa-regular fa-star star");
                        stars.eq(5).removeClass().addClass("fa-regular fa-star star");
                    } else if (halfhearts == 7) {
                        stars.eq(0).removeClass().addClass("fa-solid fa-star star");
                        stars.eq(1).removeClass().addClass("fa-solid fa-star star");
                        stars.eq(2).removeClass().addClass("fa-solid fa-star star");
                        stars.eq(3).removeClass().addClass("fa-regular fa-star-half-stroke star");
                        stars.eq(4).removeClass().addClass("fa-regular fa-star star");
                        stars.eq(5).removeClass().addClass("fa-regular fa-star star");
                    } else if (halfhearts == 8) {
                        stars.eq(0).removeClass().addClass("fa-solid fa-star star");
                        stars.eq(1).removeClass().addClass("fa-solid fa-star star");
                        stars.eq(2).removeClass().addClass("fa-solid fa-star star");
                        stars.eq(3).removeClass().addClass("fa-solid fa-star star");
                        stars.eq(4).removeClass().addClass("fa-regular fa-star star");
                        stars.eq(5).removeClass().addClass("fa-regular fa-star star");
                    } else if (halfhearts == 9) {
                        stars.eq(0).removeClass().addClass("fa-solid fa-star star");
                        stars.eq(1).removeClass().addClass("fa-solid fa-star star");
                        stars.eq(2).removeClass().addClass("fa-solid fa-star star");
                        stars.eq(3).removeClass().addClass("fa-solid fa-star star");
                        stars.eq(4).removeClass().addClass("fa-regular fa-star-half-stroke star");
                        stars.eq(5).removeClass().addClass("fa-regular fa-star star");
                    } else if (halfhearts == 10) {
                        stars.eq(0).removeClass().addClass("fa-solid fa-star star");
                        stars.eq(1).removeClass().addClass("fa-solid fa-star star");
                        stars.eq(2).removeClass().addClass("fa-solid fa-star star");
                        stars.eq(3).removeClass().addClass("fa-solid fa-star star");
                        stars.eq(4).removeClass().addClass("fa-solid fa-star star");
                        stars.eq(5).removeClass().addClass("fa-regular fa-star star");
                    } else if (halfhearts == 11) {
                        stars.eq(0).removeClass().addClass("fa-solid fa-star star");
                        stars.eq(1).removeClass().addClass("fa-solid fa-star star");
                        stars.eq(2).removeClass().addClass("fa-solid fa-star star");
                        stars.eq(3).removeClass().addClass("fa-solid fa-star star");
                        stars.eq(4).removeClass().addClass("fa-solid fa-star star");
                        stars.eq(5).removeClass().addClass("fa-regular fa-star-half-stroke star");
                    } else if (halfhearts == 12) {
                        stars.eq(0).removeClass().addClass("fa-solid fa-star star");
                        stars.eq(1).removeClass().addClass("fa-solid fa-star star");
                        stars.eq(2).removeClass().addClass("fa-solid fa-star star");
                        stars.eq(3).removeClass().addClass("fa-solid fa-star star");
                        stars.eq(4).removeClass().addClass("fa-solid fa-star star");
                        stars.eq(5).removeClass().addClass("fa-solid fa-star star");
                    }
                }
            });
            $(".stars-wrapper").on('mouseenter', '.star', function (event) {
                var star_index = $(this).index();
                stars_parent = $(this).parent();
                stars = stars_parent.children();

                if (star_index == 0) {
                    stars.eq(0).removeClass().addClass("fa-solid fa-star star");
                    stars.eq(1).removeClass().addClass("fa-regular fa-star star");
                    stars.eq(2).removeClass().addClass("fa-regular fa-star star");
                    stars.eq(3).removeClass().addClass("fa-regular fa-star star");
                    stars.eq(4).removeClass().addClass("fa-regular fa-star star");
                    stars.eq(5).removeClass().addClass("fa-regular fa-star star");
                } else if (star_index == 1) {
                    stars.eq(0).removeClass().addClass("fa-solid fa-star star");
                    stars.eq(1).removeClass().addClass("fa-solid fa-star star");
                    stars.eq(2).removeClass().addClass("fa-regular fa-star star");
                    stars.eq(3).removeClass().addClass("fa-regular fa-star star");
                    stars.eq(4).removeClass().addClass("fa-regular fa-star star");
                    stars.eq(5).removeClass().addClass("fa-regular fa-star star");
                } else if (star_index == 2) {
                    stars.eq(0).removeClass().addClass("fa-solid fa-star star");
                    stars.eq(1).removeClass().addClass("fa-solid fa-star star");
                    stars.eq(2).removeClass().addClass("fa-solid fa-star star");
                    stars.eq(3).removeClass().addClass("fa-regular fa-star star");
                    stars.eq(4).removeClass().addClass("fa-regular fa-star star");
                    stars.eq(5).removeClass().addClass("fa-regular fa-star star");
                } else if (star_index == 3) {
                    stars.eq(0).removeClass().addClass("fa-solid fa-star star");
                    stars.eq(1).removeClass().addClass("fa-solid fa-star star");
                    stars.eq(2).removeClass().addClass("fa-solid fa-star star");
                    stars.eq(3).removeClass().addClass("fa-solid fa-star star");
                    stars.eq(4).removeClass().addClass("fa-regular fa-star star");
                    stars.eq(5).removeClass().addClass("fa-regular fa-star star");
                } else if (star_index == 4) {
                    stars.eq(0).removeClass().addClass("fa-solid fa-star star");
                    stars.eq(1).removeClass().addClass("fa-solid fa-star star");
                    stars.eq(2).removeClass().addClass("fa-solid fa-star star");
                    stars.eq(3).removeClass().addClass("fa-solid fa-star star");
                    stars.eq(4).removeClass().addClass("fa-solid fa-star star");
                    stars.eq(5).removeClass().addClass("fa-regular fa-star star");
                } else if (star_index == 5) {
                    stars.eq(0).removeClass().addClass("fa-solid fa-star star");
                    stars.eq(1).removeClass().addClass("fa-solid fa-star star");
                    stars.eq(2).removeClass().addClass("fa-solid fa-star star");
                    stars.eq(3).removeClass().addClass("fa-solid fa-star star");
                    stars.eq(4).removeClass().addClass("fa-solid fa-star star");
                    stars.eq(5).removeClass().addClass("fa-solid fa-star star");
                }
            });

            //Bewertung: Gefällt mir/ Gefällt mir nicht
            $(".vote-up-ajax").click(function () {
                var icon = $(this);
                if (!icon.hasClass("voted-up")) {
                    if (ajax_vote_up) ajax_vote_up.abort();
                    ajax_vote_up = $.ajax({
                        url: '/ajax/element/vote',
                        type: 'POST',
                        dataType: 'json',
                        data: {id: icon.attr("data-id"), value: 1},
                        success: function (data) {
                            if (data) {
                                icon.addClass("voted-up");
                                icon.children().eq(1).html(data[0]);
                                icon.next().children().eq(1).html(data[1]);
                                icon.next().removeClass("voted-down");
                            } else {
                                window.location("/login");
                            }
                        }
                    });
                } else {
                    if (ajax_vote_up_remove) ajax_vote_up_remove.abort();
                    ajax_vote_up_remove = $.ajax({
                        url: '/ajax/element/removevote',
                        type: 'POST',
                        dataType: 'json',
                        data: {id: icon.attr("data-id"), type: "like"},
                        success: function (data) {
                            if (data) {
                                icon.removeClass("voted-up");
                                icon.children().eq(1).html(data[0]);
                                icon.next().children().eq(1).html(data[1]);
                            } else {
                                window.location("/login");
                            }
                        }
                    });
                }
            });
            $(".vote-down-ajax").click(function () {
                var icon = $(this);
                if (!icon.hasClass("voted-down")) {
                    if (ajax_vote_down) ajax_vote_down.abort();
                    ajax_vote_down = $.ajax({
                        url: '/ajax/element/vote',
                        type: 'POST',
                        dataType: 'json',
                        data: {id: icon.attr("data-id"), value: -1},
                        success: function (data) {
                            if (data) {
                                icon.addClass("voted-down");
                                icon.children().eq(1).html(data[1]);
                                icon.parent().children().first().children().eq(1).html(data[0]);
                                icon.parent().children().first().removeClass("voted-up");
                            } else {
                                window.location("/login");
                            }
                        }
                    });
                } else {
                    if (ajax_vote_down_remove) ajax_vote_down_remove.abort();
                    ajax_vote_down_remove = $.ajax({
                        url: '/ajax/element/removevote',
                        type: 'POST',
                        dataType: 'json',
                        data: {id: icon.attr("data-id"), value: -1, type: "like"},
                        success: function (data) {
                            if (data) {
                                icon.removeClass("voted-down");
                                icon.children().eq(1).html(data[1]);
                                icon.parent().children().first().children().eq(1).html(data[0]);
                            } else {
                                window.location("/login");
                            }
                        }
                    });
                }
            });

            //Dynamische Höhe
            $(".hover-title").each(function () {
                $(this).css({
                    'margin-top': '-' + $(this).outerHeight() + 'px'
                });
            });

            //DELETE
            $(".delete").click(function () {
                var elementid = $(this).attr("data-id");
                var remove = $(this).parent().parent().parent();

                if (ajax_delete) ajax_delete.abort();
                ajax_delete = $.ajax({
                    url: '/ajax/delete',
                    type: 'POST',
                    dataType: 'json',
                    data: {id: elementid},
                    success: function (data) {
                        if (data == "true") {
                            console.log(remove);
                            remove.remove();
                        }
                    }
                });
            });

            //EDIT
            $(".edit").click(function () {
                $("#e_date").val($(this).attr("data-date"));
                $("#e_name").val($(this).attr("data-name"));
                $("#e_info").val($(this).attr("data-info"));
                $("#edit_id").val($(this).attr("data-id"));
            });

            //FLAG
            $(".flag").click(function () {
                $(this).addClass("text-danger");
            });

            //FAVORITS
            $("#heartcount").click(function () {
                if ($(this).parent().children().first().hasClass("gold")) {
                    $(this).parent().children().first().removeClass("gold");
                    $("#heartcount").addClass("gold");
                    $("#heartcount").html("");
                    if (ajax_favorite) ajax_favorite.abort();
                    ajax_favorite = $.ajax({
                        url: '/ajax/favorite/remove',
                        type: 'POST',
                        dataType: 'json',
                        data: {id: $("#list_id").val()},
                        success: function (data) {
                            $("#heartcount").html(data);
                        }
                    });
                } else {

                    $(this).parent().children().first().addClass("gold");
                    $("#heartcount").removeClass("gold");
                    $("#heartcount").html("");
                    if (ajax_favorite) ajax_favorite.abort();
                    ajax_favorite = $.ajax({
                        url: '/ajax/favorite/add',
                        type: 'POST',
                        dataType: 'json',
                        data: {id: $("#list_id").val()},
                        success: function (data) {
                            $("#heartcount").html(data);
                        }
                    });
                }
            });
            $(".heart").click(function () {
                if ($(this).hasClass("gold")) {
                    $(this).removeClass("gold");
                    $("#heartcount").addClass("gold");
                    $("#heartcount").html("");
                    if (ajax_favorite) ajax_favorite.abort();
                    ajax_favorite = $.ajax({
                        url: '/ajax/favorite/remove',
                        type: 'POST',
                        dataType: 'json',
                        data: {id: $("#list_id").val()},
                        success: function (data) {
                            $("#heartcount").html(data);
                        }
                    });
                } else {
                    $("#heartcount").removeClass("gold");
                    $(this).addClass("gold");
                    $("#heartcount").html("");
                    if (ajax_favorite) ajax_favorite.abort();
                    ajax_favorite = $.ajax({
                        url: '/ajax/favorite/add',
                        type: 'POST',
                        dataType: 'json',
                        data: {id: $("#list_id").val()},
                        success: function (data) {
                            console.log(data);
                            $("#heartcount").html(data);
                        }
                    });
                }
            });
            $("#heartcount").hover(
                function () {
                    $(this).parent().children().first().children().first().addClass("fa-beat");
                }, function () {
                    $(this).parent().children().first().children().first().removeClass("fa-beat");
                }
            );
            //EDIT LINK
            $(".edit-link").hover(
                function () {
                    if ($(this).hasClass("heart")) {
                        $(this).children().first().addClass("fa-beat");
                    } else {
                        $(this).children().first().addClass("fa-spin-reverse fa-spin");
                    }
                }, function () {
                    if ($(this).hasClass("heart")) {
                        $(this).children().first().removeClass("fa-beat");
                    } else {
                        $(this).children().first().removeClass("fa-spin-reverse fa-spin");
                    }
                }
            );

        }

    }
);

// AJAX VARIABLES
var ajax_get_cover = null;

/* Get Cover or Image (Google Search API & TheMovieDB API)  */
function getCover(movie_name, type) {
    //type = move/ tv
    if (ajax_get_cover) ajax_get_cover.abort();

    if (type == "other") {

        ajax_get_cover = $.ajax({
            type: 'POST',
            crossDomain: true,
            dataType: 'jsonp',
            url: 'https://customsearch.googleapis.com/customsearch/v1?key=AIzaSyCDIKchKpEux-n_VE0gsiewYq51SoKGVI8&cx=a9568d0d3c5249da2&q=' + encodeURI(movie_name) + '&searchType=image&num=2&rights=(cc_publicdomain%7Ccc_attribute%7Ccc_sharealike%7Ccc_nonderived).-(cc_noncommercial)',
            success: function (data) {
                if (data.items != undefined && data.items[0] != undefined && data.items[1] != undefined) {

                    var imgurl = data.items[0].link;
                    var imgurl2 = data.items[1].link;

                    $("#imgpreview").html('<div class="w-100 mt-3"><label class=\"form-label text-muted\">Bild auswählen:</label><br></div>' +
                        '<div class="img-preview-cover position-relative h-100 w-49 rounded overflow-hidden pointer"><img class="cover img-fluid" src="' + imgurl + '">' +
                        '</div>' +
                        '<div class="img-preview-cover position-relative w-49 rounded overflow-hidden pointer"><img class="cover img-fluid" src="' + imgurl2 + '">' +
                        '</div>');
                } else if (data.items != undefined && data.results[0] != undefined) {

                    var imgurl = data.items[0].link;

                    $("#imgpreview").html('<div class="w-100 mt-3"><label class=\"form-label text-muted\">Bild auswählen:</label><br></div><div class="img-preview-cover h-100 position-relative w-49 rounded overflow-hidden pointer"><img class="cover img-fluid" src="' + imgurl + '">' +
                        '</div>');
                }


            }
        });

    } else if (type == "games") {

        $.get(
            "https://api.rawg.io/api/games?key=69ab11b789c64cf1a210a29af0f3793b&search=" + encodeURI(movie_name),
            function (data) {

                if (data["results"][0] != undefined && data["results"][1] != undefined) {

                    $("#imgpreview").html('<div class="w-100 mt-3"><label class=\"form-label  text-muted\">Bild auswählen:</label><br></div>' +
                        '<div class="img-preview-cover position-relative w-49 rounded overflow-hidden pointer"><img data-rel="' + encodeURI(data["results"][0]["released"]) + '" data-name="' + encodeURI(data["results"][0]["name"]) + '" class="cover img-fluid" src="' + data["results"][0]["background_image"] + '">' +
                        '<div class="hover-title-prev rounded-bottom" style="pointer-events: none">' + data["results"][0]["name"] + '</div></div>' +
                        '<div class="img-preview-cover position-relative w-49 rounded overflow-hidden pointer"><img data-rel="' + encodeURI(data["results"][1]["released"]) + '" data-name="' + encodeURI(data["results"][1]["name"]) + '" class="cover img-fluid" src="' + data["results"][1]["background_image"] + '">' +
                        '<div class="hover-title-prev rounded-bottom" style="pointer-events: none" >' + data["results"][1]["name"] + '</div></div>');

                } else if (data["results"][0] != undefined) {

                    $("#imgpreview").html('<div class="w-100 mt-3"><label class=\"form-label  text-muted\">Bild auswählen:</label><br></div>' +
                        '<div class="img-preview-cover position-relative w-49 rounded overflow-hidden pointer"><img data-rel="' + encodeURI(data["results"][0]["released"]) + '" data-name="' + encodeURI(data["results"][0]["name"]) + '" class="cover img-fluid" src="' + data["results"][0]["background_image"] + '">' +
                        '<div class="hover-title-prev rounded-bottom" style="pointer-events: none" >' + data["results"][0]["name"] + '</div></div>');
                }


            }
        );

    } else {

        ajax_get_cover = $.get(
            "https://api.themoviedb.org/3/search/" + type + "?api_key=03d35edbfcb4f2847cf809209109a85a&maxResults=2&query=" + encodeURI(movie_name),
            function (data) {
                console.log(data);

                if (data.results[0] != undefined && data.results[1] != undefined) {

                    var title1 = encodeURI(data.results[0].original_title);
                    var title2 = encodeURI(data.results[1].original_title);

                    if (type == "tv") {
                        title1 = encodeURI(data.results[0].original_name);
                        title2 = encodeURI(data.results[1].original_name);
                    }

                    var imgurl = "https://image.tmdb.org/t/p/w500" + data.results[0].poster_path;
                    var imgurl2 = "https://image.tmdb.org/t/p/w500" + data.results[1].poster_path;
                    $("#imgpreview").html('<div class="w-100 mt-3"><label class=\"form-label  text-muted\">Bild auswählen:</label><br></div>' +
                        '<div class="img-preview-cover position-relative w-49 rounded overflow-hidden pointer"><img data-name="' + title1 + '" class="cover img-fluid" src="' + imgurl + '">' +
                        '<div class="hover-title-prev rounded-bottom" style="pointer-events: none">' + decodeURI(title1) + '</div></div>' +
                        '<div class="img-preview-cover position-relative w-49 rounded overflow-hidden pointer"><img data-name="' + title2 + '" class="cover img-fluid" src="' + imgurl2 + '">' +
                        '<div class="hover-title-prev rounded-bottom" style="pointer-events: none">' + decodeURI(title2) + '</div></div>');
                } else if (data.results[0] != undefined) {

                    var title1 = encodeURI(data.results[0].original_title);
                    if (type == "tv") {
                        title1 = encodeURI(data.results[0].original_name);
                    }
                    var imgurl = "https://image.tmdb.org/t/p/w500" + data.results[0].poster_path;

                    $("#imgpreview").html('<div class="w-100 mt-3"><label class=\"form-label\">Bild auswählen:</label><br></div><div class="img-preview-cover position-relative w-49 rounded overflow-hidden pointer"><img data-name="' + title1 + '" class="cover img-fluid" src="' + imgurl + '">' +
                        '<div class="hover-title-prev rounded-bottom" style="pointer-events: none" >' + decodeURI(title1) + '</div></div>');
                }
            }
        );

    }
}

/* Get YouTube Trailer (YouTube API) */
function showTrailer(movie_name) {


    var covermode = $("#covermode").val();
    var search_append = "";

    if (covermode == "tv") {
        search_append = "%20show%20trailer";
    } else if (covermode == "movies") {
        search_append = "%20movie%20trailer";
    } else if (covermode == "games") {
        search_append = "%20game%20trailer";
    }
    $("#trailer").html("<i class=\"fa-solid fa-spinner fa-spin\"></i>");
    $("#amazon").attr("href", "https://amazon.de/s?k=" + movie_name);
    $("#netflix").attr("href", "https://www.netflix.com/search?q=" + movie_name);
    $("#steam").attr("href", "https://store.steampowered.com/search?term=" + movie_name);

    $.get(
        "https://www.googleapis.com/youtube/v3/search?part=snippet&q=" + encodeURI(movie_name) + search_append + "&maxResults=1&key=AIzaSyCDIKchKpEux-n_VE0gsiewYq51SoKGVI8",
        function (data) {
            $.each(data.items, function (i, item) {
                $("#trailer").html('<iframe class="trailerIframe" width="1140" height="623" src="https://www.youtube.com/embed/' + item.id.videoId + '" title="YouTube video player"  frameBorder="0"' +
                    'allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowFullScreen></iframe>');
            })
        }
    );
}

/* Helper für die 6 Sterne Bewertung */
function refreshStars(stars, halfhearts) {
    if (halfhearts == 0) {
        stars.html(
            '<i class="fa-regular fa-star star"></i>' +
            '<i class="fa-regular fa-star star"></i>' +
            '<i class="fa-regular fa-star star"></i>' +
            '<i class="fa-regular fa-star star"></i>' +
            '<i class="fa-regular fa-star star"></i>' +
            '<i class="fa-regular fa-star star"></i>');
    } else if (halfhearts == 2) {
        stars.html(
            '<i class="fa-solid fa-star star"></i>' +
            '<i class="fa-regular fa-star star"></i>' +
            '<i class="fa-regular fa-star star"></i>' +
            '<i class="fa-regular fa-star star"></i>' +
            '<i class="fa-regular fa-star star"></i>' +
            '<i class="fa-regular fa-star star"></i>');
    } else if (halfhearts == 3) {
        stars.html(
            '<i class="fa-solid fa-star star"></i>' +
            '<i class="fa-regular fa-star-half-stroke star"></i>' +
            '<i class="fa-regular fa-star star"></i>' +
            '<i class="fa-regular fa-star star"></i>' +
            '<i class="fa-regular fa-star star"></i>' +
            '<i class="fa-regular fa-star star"></i>');
    } else if (halfhearts == 4) {
        stars.html(
            '<i class="fa-solid fa-star star"></i>' +
            '<i class="fa-solid fa-star star"></i>' +
            '<i class="fa-regular fa-star star"></i>' +
            '<i class="fa-regular fa-star star"></i>' +
            '<i class="fa-regular fa-star star"></i>' +
            '<i class="fa-regular fa-star star"></i>');
    } else if (halfhearts == 5) {
        stars.html(
            '<i class="fa-solid fa-star star"></i>' +
            '<i class="fa-solid fa-star star"></i>' +
            '<i class="fa-regular fa-star-half-stroke star"></i>' +
            '<i class="fa-regular fa-star star"></i>' +
            '<i class="fa-regular fa-star star"></i>' +
            '<i class="fa-regular fa-star star"></i>');
    } else if (halfhearts == 6) {
        stars.html(
            '<i class="fa-solid fa-star star"></i>' +
            '<i class="fa-solid fa-star star"></i>' +
            '<i class="fa-solid fa-star star"></i>' +
            '<i class="fa-regular fa-star star"></i>' +
            '<i class="fa-regular fa-star star"></i>' +
            '<i class="fa-regular fa-star star"></i>');
    } else if (halfhearts == 7) {
        stars.html(
            '<i class="fa-solid fa-star star"></i>' +
            '<i class="fa-solid fa-star star"></i>' +
            '<i class="fa-solid fa-star star"></i>' +
            '<i class="fa-regular fa-star-half-stroke star"></i>' +
            '<i class="fa-regular fa-star star"></i>' +
            '<i class="fa-regular fa-star star"></i>');
    } else if (halfhearts == 8) {
        stars.html(
            '<i class="fa-solid fa-star star"></i>' +
            '<i class="fa-solid fa-star star"></i>' +
            '<i class="fa-solid fa-star star"></i>' +
            '<i class="fa-solid fa-star star"></i>' +
            '<i class="fa-regular fa-star star"></i>' +
            '<i class="fa-regular fa-star star"></i>');
    } else if (halfhearts == 9) {
        stars.html(
            '<i class="fa-solid fa-star star"></i>' +
            '<i class="fa-solid fa-star star"></i>' +
            '<i class="fa-solid fa-star star"></i>' +
            '<i class="fa-solid fa-star star"></i>' +
            '<i class="fa-regular fa-star-half-stroke star"></i>' +
            '<i class="fa-regular fa-star star"></i>');
    } else if (halfhearts == 10) {
        stars.html(
            '<i class="fa-solid fa-star star"></i>' +
            '<i class="fa-solid fa-star star"></i>' +
            '<i class="fa-solid fa-star star"></i>' +
            '<i class="fa-solid fa-star star"></i>' +
            '<i class="fa-solid fa-star star"></i>' +
            '<i class="fa-regular fa-star star"></i>');
    } else if (halfhearts == 11) {
        stars.html(
            '<i class="fa-solid fa-star star"></i>' +
            '<i class="fa-solid fa-star star"></i>' +
            '<i class="fa-solid fa-star star"></i>' +
            '<i class="fa-solid fa-star star"></i>' +
            '<i class="fa-solid fa-star star"></i>' +
            '<i class="fa-regular fa-star-half-stroke star"></i>');
    } else if (halfhearts == 12) {
        stars.html(
            '<i class="fa-solid fa-star star"></i>' +
            '<i class="fa-solid fa-star star"></i>' +
            '<i class="fa-solid fa-star star"></i>' +
            '<i class="fa-solid fa-star star"></i>' +
            '<i class="fa-solid fa-star star"></i>' +
            '<i class="fa-solid fa-star star"></i>');
    }
}