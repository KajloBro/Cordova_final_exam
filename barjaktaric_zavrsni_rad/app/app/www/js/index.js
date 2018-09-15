var app = {
    page: '',

    // Application Constructor
    initialize: function(data) {
        this.page = data;
        document.addEventListener('deviceready', this.onDeviceReady.bind(this), false);
    },

    onDeviceReady: function(data) {

        switch (this.page) {
          case "signin": login_listener(); break;
          case "signup": signup_listener(); break;
          case "my-profile":  signout_listener();
                              bind_profile_data(this.page);
                              post_form_listener();
                              delete_post_listener();
                              friends_listener(); break;
          case "search-profile":  search_profile_listener(); break;
          case "profile-browse":  bind_profile_browse_data();
                                  load_follow_btn();
                                  load_browse_profile_posts();
                                  follow_listener();
                                  break;
          case "friends-list":    bind_friends_list_data(this.page); break;
          case "profile-photo": profile_photo_listener(); break;
        }
    }
};

var page = $('body').data('page');
var preloader = $('.preloader-container');
app.initialize(page);
var myStorage = window.localStorage;
var domainBase = "http://sn.inntouch.net/";
var ajax_PHP_API = domainBase + "ajax.php";

$(document).bind('ajaxComplete', function () {
  preloader.hide();
});


function set_credentials(data) {
  myStorage.setItem('id', data['id']);
  myStorage.setItem('username', data['username']);
  myStorage.setItem('firstName', data['firstName']);
  myStorage.setItem('lastName', data['lastName']);
  myStorage.setItem('email', data['email']);
  myStorage.setItem('signedin', 1);

  if (data['profilePicture'] == null) myStorage.setItem('profilePicture', "img/profile.jpg");
  else myStorage.setItem('profilePicture', domainBase + data['profilePicture']);

  window.location.replace("profile.html");
}


function login_listener() {
  if (myStorage.getItem('id') !== null) window.location.replace('profile.html'); //If user is already signed in, redirect to profile

  $("#login-form").on("submit", function(event) {
    event.preventDefault();

    var data =  $(this).serialize() +
                "&ajax=1" +
                "&action=signin";

    $.ajax({
      type: "POST",
      url: ajax_PHP_API,
      data: data,
      dataType: "json",
      cache: false,
    })
    .done(function(data) {
      if (data['status'] == "success") {
        set_credentials(data);
      }
      else if (data['status'] == "fail") {
        navigator.notification.alert(
          'Username or password is incorrect.',  // message
          function(){},         // callback
          'Signin failed',      // title
          'OK'                  // buttonName
        );
      }
    })
    .fail(function() {
      alert("Could not connect to server. Please try again later");
    });
  });
}


function signup_listener() {
  $("#signup-form").on("submit", function(event) {
    event.preventDefault();

    var data = $(this).serialize() + "&ajax=1" + "&action=signup";
    $.ajax({
      type: "POST",
      url: ajax_PHP_API,
      data: data,
      dataType: "json",
      cache: true,
    })
    .done(function(data) {
      if (data['status'] == "success") {
        set_credentials(data);
      } else alert("Failed to sign up. Please try again.");
      })
    .fail(function() {
      alert("Could not connect to server. Please try again later");
    });
  });
}


function signout_listener() {
  $('.logout-btn').on("click", function(event) {
    event.preventDefault();
    myStorage.clear();
    window.location.replace("index.html");
  });
}


function bind_profile_data(page) {
  var firstName = myStorage.getItem('firstName');
  var lastName = myStorage.getItem('lastName');

  if (myStorage.getItem('profilePicture') == "reload") reload_profile_photo();

  $('[data-name]').append(firstName + ' ' + lastName);
  $('[data-profilePicture]').css('background-image', 'url(' + myStorage.getItem('profilePicture') + ')');

  load_followings_num(page);
  load_followers_num(page);
  load_posts();
}


function reload_profile_photo() {
  var userid = myStorage.getItem('id');
  var data = "id=" +
              userid +
              "&ajax=1" +
              "&action=reload-profile-photo";

  $.ajax({
    type: "POST",
    url: ajax_PHP_API,
    data: data,
    //dataType: "html",
    cache: false,
  })
  .done(function(data) {
    var photoURL = domainBase + data;
    myStorage.setItem('profilePicture', photoURL);
    $('[data-profilePicture]').css('background-image', 'url(' + myStorage.getItem('profilePicture') + ')');
  })
  .fail(function() {
    alert("Could not connect to server. Please try again later");
  });
}


function load_followings_num(page) {
  var page = page;
  var userid = myStorage.getItem('id');
  var data = "id=" +
              userid +
              "&ajax=1" +
              "&action=get-followings-num";

  $.ajax({
    type: "POST",
    url: ajax_PHP_API,
    data: data,
    dataType: "html",
    cache: false,
  })
  .done(function(data) {
    if (page == "my-profile") $('[data-followings-num]').html(data);
    else if (page == "friends-list") $('[data-friends-num]').html(data);
  })
  .fail(function() {
    alert("Could not connect to server. Please try again later");
  });
}


function load_followers_num(page) {
  var page = page;
  var userid = myStorage.getItem('id');
  var data = "id=" +
              userid +
              "&ajax=1" +
              "&action=get-followers-num";

  $.ajax({
    type: "POST",
    url: ajax_PHP_API,
    data: data,
    dataType: "html",
    cache: false,
  })
  .done(function(data) {
    if (page == "my-profile") $('[data-followers-num]').html(data);
    else if (page == "friends-list") $('[data-friends-num]').html(data);
  })
  .fail(function() {
    alert("Could not connect to server. Please try again later");
  });
}


function load_posts() {
  var userid = myStorage.getItem('id');
  var data = "id=" +
              userid +
              "&ajax=1" +
              "&action=loadPosts";

  $.ajax({
    type: "POST",
    url: ajax_PHP_API,
    data: data,
    dataType: "html",
    cache: false,
  })
  .done(function(data) {
    $('.posts-wrapper').html(data);
  })
  .fail(function() {
    alert("Could not connect to server. Please try again later");
  });
}


function post_form_listener() {
  $(".post-submit-btn").on("click", function(event) {
    event.preventDefault();

    var userid = myStorage.getItem('id');
    var data = $('#post-form').serialize() +
                "&id=" +
                userid +
                "&ajax=1" +
                "&action=new-post";

    $.ajax({
      type: "POST",
      url: ajax_PHP_API,
      data: data,
      cache: false,
    })
    .done(function(data) {
      $('#post-form input, #post-form textarea').val(''); // Reset input fields
      load_posts();
    })
    .fail(function() {
      alert("Could not connect to server. Please try again later");
    });
  });
}


function delete_post_listener() {
  $(".posts-wrapper").on("click", ".delete-post-button", function(event) {
    event.preventDefault();
    var postid = $(this).data('post-id');

    navigator.notification.confirm(
      '',                   // message
      onConfirm,            // callback to invoke with index of button pressed
      'Delete the post?',   // title
      ['Cancel','Confirm']  // buttonLabels
    );

    function onConfirm(buttonIndex) {
      if (buttonIndex == 1) {} // do nothing when cancelled
      else if (buttonIndex == 2) {
        var data =  "id=" +
                    postid +
                    "&ajax=1" +
                    "&action=delete-post";

        $.ajax({
          type: "POST",
          url: ajax_PHP_API,
          data: data,
          cache: false,
        })
        .done(function(data) {
          load_posts();
        })
        .fail(function() {
          alert("Could not connect to server. Please try again later");
        });
      }
    }
  });
}


function search_profile_listener() {
  preloader.hide();
  $("#search-profile-form").on("submit", function(event) {
    event.preventDefault();
    preloader.show();
    var data = $(this).serialize() +
                "&ajax=1" +
                "&action=search-profile" +
                "&domainBase=" +
                domainBase;

    $.ajax({
      type: "POST",
      url: ajax_PHP_API,
      data: data,
      dataType: "html",
      cache: false,
    })
    .done(function(data) {
      $('.search-profile-main-container').html(data);
      preloader.hide();

      $('.search-profile-main-container').on("click", ".search-profile-wrapper a", function(event) {
        event.preventDefault();
        var profileid = $(this).data('profile-id')
        myStorage.setItem('browse-profile-id', profileid);

        if (myStorage.getItem('id') == myStorage.getItem('browse-profile-id')) {
          window.location.replace('profile.html');
        } else window.location.replace('profile-browse.html');
      });
    })
    .fail(function() {
      alert("Could not connect to server. Please try again later");
    });
  });
}


function friends_listener() {
  /**
    * friends-list: 0 - get followings list
    * friends-lsit: 1 - get followers list
  **/
  $('[data-followings-num]').on("click", function(event) {
    event.preventDefault();
    myStorage.setItem('friends-list', 0);
    window.location.replace('friends.html');
  });

  $('[data-followers-num]').on("click", function(event) {
    event.preventDefault();
    myStorage.setItem('friends-list', 1);
    window.location.replace('friends.html');
  });
}


function bind_profile_browse_data() {
  var data = "id=" +
              myStorage.getItem('browse-profile-id') +
              "&ajax=1" +
              "&action=browse-profile" +
              "&domainBase=" +
              domainBase;

  $.ajax({
    type: "POST",
    url: ajax_PHP_API,
    data: data,
    dataType: "html",
    cache: false,
  })
  .done(function(data) {
    $('.profile-data').html(data);
  })
  .fail(function() {
    alert("Could not connect to server. Please try again later");
  });
}


function load_follow_btn() {
  var data = "id=" +
              myStorage.getItem('id') +
              "&browse-profile-id=" +
              myStorage.getItem('browse-profile-id') +
              "&ajax=1" +
              "&action=load-follow-btn";

  $.ajax({
    type: "POST",
    url: ajax_PHP_API,
    data: data,
    dataType: "html",
    cache: false,
  })
  .done(function(data) {
    $('.follow-btn-wrapper').html(data);
  })
  .fail(function() {
    alert("Could not connect to server. Please try again later");
  });
}


function load_browse_profile_posts() {
  var data = "id=" +
              myStorage.getItem('browse-profile-id') +
              "&ajax=1" +
              "&action=load-browse-profile-posts";

  $.ajax({
    type: "POST",
    url: ajax_PHP_API,
    data: data,
    dataType: "html",
    cache: false,
  })
  .done(function(data) {
    $('.posts-wrapper').html(data);
  })
  .fail(function() {
    alert("Could not connect to server. Please try again later");
  });
}


function follow_listener() {
  $('.follow-btn-wrapper').on("click", ".remove-friend-btn, .add-friend-btn", function(event) {
    event.preventDefault();
    var action;

    if ($(this).data('follow-action') == "unfollow") action = "unfollow";
    else if ($(this).data('follow-action') == "follow") action = "follow";

    data =  "id=" +
            myStorage.getItem('id') +
            "&browse-profile-id=" +
            myStorage.getItem('browse-profile-id') +
            "&ajax=1" +
            "&action=" +
            action;

    $.ajax({
        type: "POST",
        url: ajax_PHP_API,
        data: data,
        cache: false,
      })
      .done(function(data) {
        load_follow_btn();
      })
      .fail(function() {
        alert("Could not connect to server. Please try again later");
      });
  })
}


function bind_friends_list_data(page) {
  if (myStorage.getItem('friends-list') == 0) {
    load_followings_num(page);
    load_friends_list("list-followings");
  }
  else if (myStorage.getItem('friends-list') == 1) {
    load_followers_num(page);
    load_friends_list("list-followers");
  }
}


function load_friends_list(type) {
  var type = type;
  var data;

  if (type == "list-followings") {
    data = "id=" +
            myStorage.getItem('id') +
            "&ajax=1" +
            "&action=list-friends" +
            "&type=" +
            type +
            "&domainBase=" +
            domainBase;
  }
  else if (type == "list-followers") {
    data = "id=" +
            myStorage.getItem('id') +
            "&ajax=1" +
            "&action=list-friends" +
            "&type=" +
            type +
            "&domainBase=" +
            domainBase;
  }

  $.ajax({
    type: "POST",
    url: ajax_PHP_API,
    data: data,
    dataType: "html",
    cache: false,
  })
  .done(function(data) {
    $('.friendships-main-container').html(data);
    friends_profile_listener();
    })
  .fail(function() {
    alert("Could not connect to server. Please try again later");
  });
}


function friends_profile_listener() {
  $('.friendships-main-container').on("click", ".friendships-profile-wrapper a", function(event) {
    event.preventDefault();
    var profileid = $(this).data('profile-id')
    myStorage.setItem('browse-profile-id', profileid);

    if (myStorage.getItem('id') == myStorage.getItem('browse-profile-id')) {
      window.location.replace('profile.html');
    } else window.location.replace('profile-browse.html');
  });
}


function profile_photo_listener() {
  $('#open-camera-btn, #open-gallery-btn').on("click", function(event) {
    event.preventDefault();
    var sourceType;

    if ($(this).attr('id') == "open-camera-btn") sourceType = 1;
    else if ($(this).attr('id') == "open-gallery-btn") sourceType = 0;

    var options = {
      quality: 100,
      sourceType: sourceType, //0 album, 1 camera
      mediaType: 0,           //0 pictures only
      destinationType: 1,     //1 FILE_URI
      correctOrientation: true,
      saveToPhotoAlbum: true
    };

    navigator.camera.getPicture(onSuccess, onFail, options);

    function onSuccess(imageURI) {
      var win = function (r) {
        // alert("Profile photo updated successfully");
        myStorage.setItem('profilePicture', "reload");
        window.location.replace('profile.html');
      }

      var fail = function (error) {
        alert("An error has occurred: Code = " + error.code);
      }

      var ft = new FileTransfer();
      var serverURL = ajax_PHP_API +
                      "?action=upload-profile-photo&ajax=1&id=" +
                      myStorage.getItem('id');

      ft.upload(imageURI,
                encodeURI(serverURL),
                win,
                fail
      );
    }

    function onFail(message) {
      if (message != "Selection cancelled.") {
        alert('Failed because: ' + message);
      }
    }
  });
}
