use Mojolicious::Lite;

get '/register/(:movies_data)' => sub {
    my $self = shift;
    my $result = {};
    my $JSONMoviesData =  $self->stash('movies_data');

    open(my $fh, '>', 'test.txt');
	close $JSONMoviesData;

    return $self->render_json($result);
};

app->start;
