<!DOCTYPE html>
<html>
<head>
    <title>Manage Employees</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- <link rel="stylesheet" href="//cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css" /> -->
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.css" />
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/datatables/1.10.12/css/dataTables.bootstrap.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.0/css/responsive.bootstrap.min.css" type="text/css" />
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.2.1/css/buttons.bootstrap.min.css" type="text/css" />
<link rel="stylesheet" href="style.css" />
<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
<script src="//cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
<script src="//cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
<!-- Responsive extension -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>
<!-- Buttons extension -->
<script src="//cdn.datatables.net/buttons/1.2.1/js/dataTables.buttons.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.2.1/js/buttons.bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.2.1/js/buttons.html5.min.js"></script>
</head>
<body>

<div class="container">
    <h1>Manage Employees</h1>
    <div class="row"><div class="col-md-12"><div id="msg_content"></div></div></div>
    <form id="postForm" name="postForm" class="form-horizontal">
       <input type="hidden" name="id" id="id">
        <div class="form-group">
          <div class="col-md-6">
             <label for="Name" class="col-sm-4 control-label">Name</label>
              <div class="col-sm-8">
                  <input placeholder="Enter Name" class="form-control" id="name" maxlength="50" name="name" type="text">
              </div>
          </div>
        </div>
        <div class="form-group">
          <div class="col-md-6">
             <label for="Email" class="col-sm-4 control-label">Email</label>
              <div class="col-sm-8">
                  <input placeholder="Enter Email" class="form-control" id="email" maxlength="50" name="email" type="text">
              </div>
          </div>
        </div>
        <div class="form-group">
          <div class="col-md-6">
             <label for="Mobile" class="col-sm-4 control-label">Mobile</label>
              <div class="col-sm-8">
                  <input placeholder="Enter Mobile No" class="form-control" id="mobile_no" maxlength="50" name="mobile_no" type="text">
              </div>
          </div>
        </div>
        <div class="form-group">
          <div class="col-md-6">
             <label for="Department" class="col-sm-4 control-label">Department</label>
              <div class="col-sm-8">
                  <select name="department" id="department" class="form-control">
                    <option value="1">Sales</option>
                    <option value="2">Marketing</option>
                    <option value="3">IT</option>
                  </select>
              </div>
          </div>
        </div>
        <div class="form-group">
          <div class="col-md-6">
             <label for="Status" class="col-sm-4 control-label">Status</label>
              <div class="col-sm-8">
                  <select name="status" id="status" class="form-control">
                    <option value="0">Inactive</option>
                    <option value="1">Active</option>
                    </select>
              </div>
          </div>
        </div>


        <div class="col-sm-offset-2 col-sm-10">
         <button type="submit" class="btn btn-primary" id="savedata" value="create">Add New Employee
         </button>
        </div>

    </form>
    <table class="table table-bordered data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Name</th>
                <th>Email</th>
                <th>Mobile</th>
                <th>Department</th>
                <th>Status</th>
                <th width="280px">Action</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>



</body>

<script type="text/javascript">
  $(function () {

      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });

    var table = $('.data-table').dataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('employees.index') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'name', name: 'name'},
            {data: 'email', name: 'email'},
            {data: 'mobile_no', name: 'mobile_no'},
            {data: 'department', name: 'department'},
            {data: 'status', name: 'status'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });

    $('#createNewPost').click(function () {
        $('#savedata').val("create-post");
        $('#id').val('');
        $('#postForm').trigger("reset");
        $('#modelHeading').html("Create New Post");
        $('#ajaxModelexa').modal('show');
    });

    $('body').on('click', '.editPost', function () {
      var id = $(this).data('id');
      $.get("{{ route('employees.index') }}" +'/' + id +'/edit', function (data) {

          $('#savedata').val("edit-user");
          $('#savedata').text('Update Employee');
          $('#id').val(data.id);
          $('#name').val(data.name);
          $('#email').val(data.email);
          $('#mobile_no').val(data.mobile_no);
          $('#department').val(data.department);
          $('#status').val(data.status);
      })
   });

    $('#savedata').click(function (e) {
        e.preventDefault();
        $(this).html('Sending..');

        $.ajax({
          data: $('#postForm').serialize(),
          url: "{{ route('employees.store') }}",
          type: "POST",
          dataType: 'json',
          success: function (data) {

              $('#postForm').trigger("reset");
              table.api().ajax.reload(null, false);
              //table.draw();
              $('#savedata').html('Add New Employee');
              $('#msg_content').html('<div class="alert alert-success" role="alert">'+data.success+'</div>');

          },
          error: function (data) {
              console.log('Error:', data);
              $('#savedata').html('Save Changes');
          }
      });
    });

    $('body').on('click', '.deletePost', function () {

        var id = $(this).data("id");
        confirm("Are You sure want to delete this Employee!");

        $.ajax({
            type: "DELETE",
            url: "{{ route('employees.store') }}"+'/'+id,
            success: function (data) {
              table.api().ajax.reload(null, false);
              $('#msg_content').html('<div class="alert alert-success" role="alert">'+data.success+'</div>');
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    });

  });
</script>
</html>
