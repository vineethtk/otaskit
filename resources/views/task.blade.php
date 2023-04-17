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
    <h1>Manage Task</h1>
    <div class="col-sm-offset-10 col-sm-2">
     <a class="btn btn-info" href="{{ route('employees.index') }}">View Employees</a>
    </div>
    <div class="row"><div class="col-md-12"><div id="msg_content"></div></div></div>
    <form id="postForm" name="postForm" class="form-horizontal">
       <input type="hidden" name="id" id="id">
        <div class="form-group">
          <div class="col-md-6">
             <label for="Title" class="col-sm-4 control-label">Title</label>
              <div class="col-sm-8">
                  <input placeholder="Enter Title" class="form-control" id="title" maxlength="50" name="title" type="text">
              </div>
          </div>
        </div>
        <div class="form-group">
          <div class="col-md-6">
             <label for="Description" class="col-sm-4 control-label">Description</label>
              <div class="col-sm-8">
                  <input placeholder="Enter Description" class="form-control" id="description" maxlength="50" name="description" type="text">
              </div>
          </div>
        </div>

        <!-- <div class="form-group">
          <div class="col-md-6">
             <label for="Assignee" class="col-sm-4 control-label">Assignee</label>
              <div class="col-sm-8">
                  <select name="department" id="department" class="form-control">
                    <option value="">-Select-</option>
                    @foreach($employees as $employee)
                    <option value="{{$employee->id}}">{{$employee->name}}</option>
                    @endforeach
                  </select>
              </div>
          </div>
        </div>
        <div class="form-group">
          <div class="col-md-6">
             <label for="Status" class="col-sm-4 control-label">Status</label>
              <div class="col-sm-8">
                  <select name="status" id="status" class="form-control">
                    <option value="0">Unassigned</option>
                    <option value="1">Assigned</option>
                    <option value="2">In Progress</option>
                    <option value="3">Done</option>
                    </select>
              </div>
          </div>
        </div> -->


        <div class="col-sm-offset-2 col-sm-10">
         <button type="submit" class="btn btn-primary" id="savedata" value="create">Create New Task
         </button>
        </div>

    </form>
    <br>
    <br>
    <br>
<div class="row">
    <div class="form-group">
      <div class="col-md-4">
         <label for="Assignee" class="col-sm-4 control-label">Assignee</label>
          <div class="col-sm-8">
              <select name="assignee" id="assignee" class="form-control" onchange="filter_task()">
                <option value="">-Select-</option>
                @foreach($employees as $employee)
                <option value="{{$employee->id}}">{{$employee->name}}</option>
                @endforeach
              </select>
          </div>
      </div>
      <div class="col-md-4">
         <label for="Status" class="col-sm-4 control-label">Status</label>
          <div class="col-sm-6">
              <select name="status" id="status" class="form-control" onchange="filter_task()">
                <option value="">-Select-</option>
                <option value="0">Unassigned</option>
                <option value="1">Assigned</option>
                <option value="2">In Progress</option>
                <option value="3">Done</option>
              </select>
          </div>
      </div>
    </div>
  </div>
    <table class="table table-bordered data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Title</th>
                <th>Description</th>
                <th>Assignee</th>
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

      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });

    var table = $('.data-table').dataTable({
        processing: true,
        serverSide: true,
        "bdestroy": true,
        "destroy": true,
        ajax: "{{ route('task.index') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'title', name: 'title'},
            {data: 'description', name: 'description'},
            {data: 'assignee', name: 'assignee'},
            {data: 'status', name: 'status'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });

    $('#createNewPost').click(function () {
        $('#savedata').val("create-post");
        $('#id').val('');
        $('#postForm').trigger("reset");

    });

    $('body').on('click', '.editPost', function () {
      var id = $(this).data('id');
      $.get("{{ route('employees.index') }}" +'/' + id +'/edit', function (data) {

          $('#savedata').val("edit-user");
          $('#savedata').text('Update Task');
          $('#id').val(data.id);
          $('#title').val(data.title);
          $('#description').val(data.description);
          $('#assignee').val(data.assignee);
          $('#status').val(data.status);
      })
   });

    $('#savedata').click(function (e) {
        e.preventDefault();
        $(this).html('Sending..');

        $.ajax({
          data: $('#postForm').serialize(),
          url: "{{ route('task.store') }}",
          type: "POST",
          dataType: 'json',
          success: function (data) {

              $('#postForm').trigger("reset");
              table.api().ajax.reload(null, false);
              //table.draw();
              $('#savedata').html('Create New Task');
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
        confirm("Are You sure want to delete this Task!");

        $.ajax({
            type: "DELETE",
            url: "{{ route('task.store') }}"+'/'+id,
            success: function (data) {
              table.api().ajax.reload(null, false);
              $('#msg_content').html('<div class="alert alert-success" role="alert">'+data.success+'</div>');
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    });

    function assign_employee(task_id,emp_id){

      $.ajax({

        url: "{{ route('task.store') }}"+'/'+task_id,
        data: {task_id:task_id,emp_id:emp_id,_method:'PUT',btn:1},
        type: "POST",
        dataType: 'json',
        success: function (data) {
            table.api().ajax.reload(null, false);
            $('#msg_content').html('<div class="alert alert-success" role="alert">'+data.success+'</div>');

        },
        error: function (data) {
            console.log('Error:', data);
            $('#savedata').html('Save Changes');
        }
      });

    }

    function update_status(task_id,status){
      $.ajax({

        url: "{{ route('task.store') }}"+'/'+task_id,
        data: {task_id:task_id,status:status,_method:'PUT',btn:2},
        type: "POST",
        dataType: 'json',
        success: function (data) {
            table.api().ajax.reload(null, false);
            $('#msg_content').html('<div class="alert alert-success" role="alert">'+data.success+'</div>');

        },
        error: function (data) {
            console.log('Error:', data);
            $('#savedata').html('Save Changes');
        }
      });
    }


function filter_task(){
var status = $('#status').val();
var assignee = $('#assignee').val();
  var table = $('.data-table').dataTable({
      processing: true,
      serverSide: true,
      "bdestroy": true,
      "destroy": true,
      ajax: {

        url: "{{ route('task.index') }}",

        data: {status:status,assignee:assignee}

      },
      columns: [
          {data: 'DT_RowIndex', name: 'DT_RowIndex'},
          {data: 'title', name: 'title'},
          {data: 'description', name: 'description'},
          {data: 'assignee', name: 'assignee'},
          {data: 'status', name: 'status'},
          {data: 'action', name: 'action', orderable: false, searchable: false},
      ]
  });
}

</script>
</html>
