#!/usr/bin/perl

print "Content-type:text/html\r\n\r\n";


use Template;

my $tt = Template->new({
    INCLUDE_PATH => '/var/cgi-bin/',
    INTERPOLATE  => 1,
    RELATIVE    => 1,
}) || die "$Template::ERROR\n";


my $vars = {
    name     => 'Count Edward van Halen',
    debt     => '3 riffs and a solo',
    deadline => 'the next chorus',
};

$tt->process('index.tt', $vars)
    || die $tt->error(), "\n";


1;