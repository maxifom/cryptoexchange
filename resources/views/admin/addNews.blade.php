@extends('admin.app') @section('content_admin')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <h1 style="text-align: center;">Add news</h1>
                <form action="{{route('admin_add_news')}}" method="POST">
                    <div class="form-group">
                        <label for="header">Header</label>
                        <input id='header' type="text" name='header' class="form-control" placeholder="Header" required>
                        <div class="form-group">
                            <label for="text">Text</label>
                            <textarea class="form-control" id="text" rows="5" name="text"
                                      placeholder="Text" required></textarea>
                        </div>
                    </div>
                    <button type='submit' class='btn btn-outline-primary'>Create news</button>
                    {{csrf_field()}}
                </form>
            </div>
        </div>
    </div>
@endsection