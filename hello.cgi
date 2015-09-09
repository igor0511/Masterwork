#!/usr/bin/perl

print "Content-type:text/html\r\n\r\n";
print '<html>';
print '<head>';
print '<title>Hello Word - First CGI Program</title>';
print '</head>';
print '<body>';
print '<h2>Hello Word! This is my first CGI program</h2>';


print '</body>';
print '</html>';

use Template;

my $tt = Template->new({
    INCLUDE_PATH => ]
        '/var/cgi-bin/',
        '/var/cgi-bin/css/',
    ]
    INTERPOLATE  => 1,
}) || die "$Template::ERROR\n";


my $vars = {
    CSS_URL   = "/assets/css",
};

$tt->process('test.tt', $vars)
    || die $tt->error(), "\n";


1;