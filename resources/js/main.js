($(document).ready(function() {
    var loggedUser = $('#currentUser').text();

    // events
    //------------

    // afficher form mdp oublié
    $('#forgotten a').on('click', function(e) {
        e.preventDefault();
        $('#forgotten form').fadeToggle('normal');
    });

    // sélection d'une personne
    $('.family li').on('click', function() {
        selectPerson($(this));
    });

    // changement d'année en historique
    $('.history_menu select').on('change', function() {
        var selectedYear = $('.history_menu select option:selected').val();
        updateList(selectedYear);
    });

    // ajout de cadeau
    $('.addGift').on('click', function() {
        getFormModal('gift');
    });

    // modification de cadeau
    $('.editGift').on('click', function() {
        getFormModal('gift', $(this).data('gift-id'));
    });

    // suppression de cadeau
    $('.delGift').on('click', function() {
        getFormModal('gift', $(this).data('gift-id'), true);
    });

    // ajout de suggestion
    $('.addSuggest').on('click', function() {
        getFormModal('suggestion', $(this).data('target'));
    });
    $(document).on('click', '#validSuggestionForm', function(e) {
        e.preventDefault();
        var target = $('#suggestionTarget').val();
        $.ajax({
            url: 'addSuggestion',
            type: 'POST',
            data: {
                suggestionText: $('#suggestionText').val(),
                target_id:      $('#suggestionTarget').val(),
                author_id:      $('#suggestionAuthor').val(),
                csrf_token_name: getCookieValue('pfvcsrf_cookie_name')
            }
        }).done(function() {
            $('.close').trigger('click');
            suggestionList(target);
        })
    });

    // ajout d'utilisateur
    $('.addUser').on('click', function() {
        getFormModal('user');
    });

    // modification d'utilisateur
    $('.editUser').on('click', function() {
        getFormModal('user', $(this).data('user-id'));
    });

    // suppression d'utilisateur
    $('.delUser').on('click', function() {
        getFormModal('user', $(this).data('user-id'), true);
    });

    // fermer modale
        // clic sur la croix
        $(document).on('click', '.close', function() {
            $('.modal-layer').remove();
        });

        // touche echap
        $(document).on('keydown', function(e) {
            if ($('.modal-layer').length > 0) {
                if (e.keyCode == 27) {
                    $('.modal-layer').remove();
                }
            }
        });

    // confirmation de suppression
    $(document).on('click', '.confirmN', function() {
        $('.modal-layer').remove();
    });
    $(document).on('click', '.confirmY', function() {
        var delElementId = $(this).data('element-id');
        var url = successPageUrl= '';
        if ($(this).data('element-type') === 'gift')
        {
          url = 'deleteGift';
          successPageUrl = '/pfv/index.php/page/giftList';
        }
        else if ($(this).data('element-type') === 'user')
        {
          url = 'admin/deleteUser';
          successPageUrl = '/pfv/index.php/admin';
        }

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                elementId: delElementId,
                csrf_token_name: getCookieValue('pfvcsrf_cookie_name')
            }
        }).done(function() {
            window.location.href = successPageUrl;
        })
    });

    // réserver un cadeau
    $(document).on('click', '.resa', function() {
        var giftId = $(this).data('gift-id');
        var clicked = $(this);

        $.ajax({
            url: 'reserveGift',
            type: 'POST',
            data: {
                giftId: giftId,
                reserver: loggedUser,
                csrf_token_name: getCookieValue('pfvcsrf_cookie_name')
            }
        }).done(function() {
            var loggedUserName = $('.family #fam'+loggedUser).text();
            var newLine = '<span class="reserved">Réservé par <strong>'+loggedUserName+'</strong></span> '+
                        '<button class="cancel_resa" data-gift-id="'+giftId+'">Annuler réservation</button>';
            clicked.replaceWith(newLine);
        })
    });
    $(document).on('click', '.cancel_resa', function() {
        var giftId = $(this).data('gift-id');
        var clickedParent = $(this).parent();
        $.ajax({
            url: 'reserveGift',
            type: 'POST',
            data: {
                giftId: giftId,
                reserver: 0,
                csrf_token_name: getCookieValue('pfvcsrf_cookie_name')
            }
        }).done(function() {
            var newLine = '<span class="resa" data-gift-id="'+giftId+'">Réserver</span>';
            clickedParent.find('.reserved').remove();
            clickedParent.find('.cancel_resa').replaceWith(newLine);
        })
    });

    // supprimer une suggestion
    $(document).on('click', '.del_suggest', function() {
        var suggestId = $(this).data('id');
        var clickedParent = $(this).parent();
        $.ajax({
            url: 'deleteSuggestion',
            type: 'POST',
            data: {
                suggestionId: suggestId,
                csrf_token_name: getCookieValue('pfvcsrf_cookie_name')
            }
        }).done(function() {
            clickedParent.fadeOut('normal', function() {
                $(this).remove();
            });
        })
    });

    // fonctions
    //---------------
    function selectPerson(selection) {
        $('.family li').removeClass('selectedPerson');
        selection.addClass('selectedPerson');

        var activeUser = selection.attr('id');
        activeUser = activeUser.replace('fam', '');
        var activeUserName = selection.text();

        // maj du titre
        $('main h3 span').replaceWith('<span>'+activeUserName+'</span>');
        $('main h3 span').css('text-transform', 'capitalize');

        if (!selection.parent().hasClass('histo')) {
            $('#blockSuggestion h3 span').replaceWith('<span>'+activeUserName+'</span>');
            $('#blockSuggestion h3 span').css('text-transform', 'capitalize');
            $('.addSuggest').attr('data-target', activeUser);

            // appel ajax pour avoir les suggestions du user
            suggestionList(activeUser);
        }

        // maj de la liste
        $('main div[id^=list_]').addClass('hidden');
        $('#list_'+activeUser).removeClass('hidden');

        if (activeUser != loggedUser) {
            $('#blockSuggestion').show();
            $('main .lastAction').hide();
        } else {
            $('#blockSuggestion').hide();
            $('main .lastAction').show();
        }
    }

    // appel ajax pour récupérer la liste pour l'année
    function updateList(year) {
        $.ajax({
            url: 'listByYear',
            type: 'POST',
            data: {
                year: year,
                csrf_token_name: getCookieValue('pfvcsrf_cookie_name')
            }
        }).done(function(data) {
            $('.giftList').html(data);
            selectPerson($('.selectedPerson'));
        })
    }

    // appel ajax pour récupérer la liste des suggestions
    function suggestionList(target) {
        $.ajax({
            url: 'getSuggestionList',
            type: 'POST',
            data: {
                targetId: target,
                csrf_token_name: getCookieValue('pfvcsrf_cookie_name')
            }
        }).done(function(data) {
            $('.contentSuggestions').html(data);
        })
    }

    // lire un cookie par son nom
    function getCookieValue(a) {
        var b = document.cookie.match('(^|;)\\s*' + a + '\\s*=\\s*([^;]+)');
        return b ? b.pop() : '';
    }

    // crée une modal et récupère son contenu via ajax
    function getFormModal(formType, elementId = 0, $del = false) {
        // base de la modal
        var modalStart = '<div class="modal-layer">'+
                            '<div class="modal-content">'+
                                '<span class="close">&times;</span>';
        var content = '';
        var modalEnd = '</div>'+
                    '</div>';

        if (formType == 'gift') {
            if (!$del) {
                $.ajax({
                    url: 'getModalForm',
                    type: 'POST',
                    data: {
                        formType: 'gift',
                        elementId: elementId,
                        csrf_token_name: getCookieValue('pfvcsrf_cookie_name')
                    }
                }).done(function(data) {
                    $('body').append(modalStart+data+modalEnd);
                })
            } else {
                $.ajax({
                    url: 'getModalConfirm',
                    type: 'POST',
                    data: {
                        elementId: elementId,
                        csrf_token_name: getCookieValue('pfvcsrf_cookie_name')
                    }
                }).done(function(data) {
                    $('body').append(modalStart+data+modalEnd);
                })
            }
        } else if (formType == 'suggestion') {
            if (!$del) {
                $.ajax({
                    url: 'getModalForm',
                    type: 'POST',
                    data: {
                        formType: 'suggestion',
                        elementId: elementId,
                        csrf_token_name: getCookieValue('pfvcsrf_cookie_name')
                    }
                }).done(function(data) {
                    $('body').append(modalStart+data+modalEnd);
                })
            }
        } else if (formType == 'user') {
            if (!$del) {
                $.ajax({
                    url: 'admin/getModalForm',
                    type: 'POST',
                    data: {
                        formType: 'user',
                        elementId: elementId,
                        csrf_token_name: getCookieValue('pfvcsrf_cookie_name')
                    }
                }).done(function(data) {
                    $('body').append(modalStart+data+modalEnd);
                })
            } else {
                $.ajax({
                    url: 'admin/getModalConfirm',
                    type: 'POST',
                    data: {
                        elementId: elementId,
                        csrf_token_name: getCookieValue('pfvcsrf_cookie_name')
                    }
                }).done(function(data) {
                    $('body').append(modalStart+data+modalEnd);
                })
            }
        }
    }
}));
