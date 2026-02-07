<?php
use Illuminate\Support\Facades\Route;;
$routeName    = Route::current();
$pageName     = explode("/", $routeName->uri());
$pageSegment  = $pageName[0];
$pageFunction = ((count($pageName)>2)?$pageName[1]:'');
// dd($routeName);
// echo $pageSegment;
// echo $pageFunction;
// print_r($pageName);die;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?=$head?>
  <style type="text/css">
    a.nav-link.active {
      color: #4154f1;
      background: #f6f9ff;
    }
    .sidebar-nav .nav-link {
      background: none;
      color: #012970;
    }
  </style>
</head>
<body>
  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">
    <?=$header?>
  </header><!-- End Header -->
  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">
    <?=$sidebar?>
  </aside><!-- End Sidebar-->
  <main id="main" class="main">
    <?=$maincontent?>
  </main><!-- End #main -->
  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <?=$footer?>
  </footer><!-- End Footer -->
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
  <!-- Vendor JS Files -->
  <script src="<?=env('ADMIN_ASSETS_URL')?>assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="<?=env('ADMIN_ASSETS_URL')?>assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="<?=env('ADMIN_ASSETS_URL')?>assets/vendor/chart.js/chart.umd.js"></script>
  <script src="<?=env('ADMIN_ASSETS_URL')?>assets/vendor/echarts/echarts.min.js"></script>
  <script src="<?=env('ADMIN_ASSETS_URL')?>assets/vendor/quill/quill.min.js"></script>
  <script src="<?=env('ADMIN_ASSETS_URL')?>assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="<?=env('ADMIN_ASSETS_URL')?>assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="<?=env('ADMIN_ASSETS_URL')?>assets/vendor/php-email-form/validate.js"></script>
  <!-- Template Main JS File -->
  <script src="<?=env('ADMIN_ASSETS_URL')?>assets/js/main.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
  <script type="text/javascript">
    $(function(){
      $('.autohide').delay(5000).fadeOut('slow');
    })
  </script>

<?php if(($pageSegment == 'notification' && ($pageFunction == 'add' || $pageFunction == 'edit')) || ($pageSegment == 'newsletter' && ($pageFunction == 'add' || $pageFunction == 'edit'))) {?>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.js"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
  <script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css">

  <!-- Initialize the plugin: -->
  <script type="text/javascript">
    $(document).ready(function() {
      $('#to_users').multiselect({
        includeSelectAllOption: true,
        enableFiltering: true,
      });
      $('#users1').multiselect({
        includeSelectAllOption: true,
        enableFiltering: true,
      });
    });
  </script>
  <script type="text/javascript">
    function getUsers(user_type){
      if(user_type != ''){
        let url = '<?=url('/')?>/notification/get-user';
        $.ajax({
            type: "POST",
            url: url,
            data: {"_token": "{{ csrf_token() }}", user_type : user_type},
            dataType: "JSON",
            beforeSend: function () {
                
            },
            success: function (rply) {
              if(rply.status){
                let html  = '';
                $.each(rply.data.user_selects, function(key, item) {
                  html += '<option value="'+item.id+'">'+item.label+'</option>';
                });
                $('#users1').empty();
                $('#users1').append(html);
                $("#users1").multiselect("rebuild");
              }else{
                  
              }
            },
            error:function (xhr, ajaxOptions, thrownError){
              var res = xhr.responseJSON;
              if(xhr.status==404) {
                  alert("Something Went Wrong In Loading The Page !!!");
              }
            }
        });
      } else {
        alert('Please Select User Type !!!');
      }
    }
  </script>
<?php } ?>
<link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/43.1.0/ckeditor5.css" />
  <script type="importmap">
    {
        "imports": {
            "ckeditor5": "https://cdn.ckeditor.com/ckeditor5/43.1.0/ckeditor5.js",
            "ckeditor5/": "https://cdn.ckeditor.com/ckeditor5/43.1.0/"
        }
    }
  </script>
  <script type="module">
    import {
        ClassicEditor,
        Essentials,
        Bold,
        Italic,
        Strikethrough,
        Subscript,
        Superscript,
        CodeBlock,
        Font,
        Link,
        List,
        Paragraph,
        Image,
        ImageCaption,
        ImageResize,
        ImageStyle,
        ImageToolbar,
        LinkImage,
        PictureEditing,
        ImageUpload,
        CloudServices,
        CKBox,
        CKBoxImageEdit,
        SourceEditing,
        ImageInsert
    } from 'ckeditor5';

    for (let i = 0; i <= 15; i++) {
      ClassicEditor
        .create( document.querySelector( '#ckeditor' + i ), {
          plugins: [ Essentials, Bold, Italic, Strikethrough, Subscript, Superscript, CodeBlock, Font, Link, List, Paragraph, Image, ImageToolbar, ImageCaption, ImageStyle, ImageResize, LinkImage, PictureEditing, ImageUpload, CloudServices, CKBox, CKBoxImageEdit, SourceEditing, ImageInsert ],
          toolbar: {
            items: [
              'undo', 'redo',
              '|',
              'heading',
              '|',
              'sourceEditing',
              '|',
              'fontfamily', 'fontsize', 'fontColor', 'fontBackgroundColor', 'formatPainter',
              '|',
              'bold', 'italic', 'strikethrough', 'subscript', 'superscript', 'code',
              '|',
              'link', 'uploadImage', 'blockQuote', 'codeBlock',
              '|',
              'bulletedList', 'numberedList', 'todoList', 'outdent', 'indent',
              '|',
              'ckbox', 'ckboxImageEdit', 'toggleImageCaption', 'imageTextAlternative', 'ckboxImageEdit',
              '|',
              'imageStyle:block',
              'imageStyle:side',
              '|',
              'toggleImageCaption',
              'imageTextAlternative',
              '|',
              'linkImage', 'insertImage', 'insertImageViaUrl'
            ]
          },
          menuBar: {
            isVisible: true
          }
        })
        .then( /* ... */ )
        .catch( /* ... */ );
    }
  </script>
</body>
</html>