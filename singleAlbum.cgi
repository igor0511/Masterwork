#!/usr/bin/perl

print "Content-type:text/html\r\n\r\n";


use CGI;
my $q = CGI->new;

my $AlbumName = $q->param('AlbumName');
my $Number = $q->param('Number') // 0;

use Template;

my $tt = Template->new({
    INCLUDE_PATH => '/var/cgi-bin/',
    INTERPOLATE  => 1,
    RELATIVE    => 1,
}) || die "$Template::ERROR\n";


my $FilePath = "192.168.0.14://wdmycloud/public/";
use POSIX;
  use Filesys::SmbClient;
  
  my $smb = new Filesys::SmbClient();
  
  my @AvailableFolders;
  
  my $Image;
  # Read a file
  my $fd = $smb->open("smb://192.168.0.14/Public/", '0666');

  my $fd = $smb->opendir("smb://192.168.0.14/Public/Shared\ Pictures/\Q$AlbumName\E");
  
  my $Status = 0;
  while (my $f = $smb->readdir_struct($fd)) {

  if ($f->[0] == SMBC_FILE) {
     if ( $Status > $Number ) {
       $Image = "192.168.0.14/images/\Q$AlbumName\E/".$f->[1];
       last;
     }
   }
      $Status++;
  }
 #close($fd);

my $vars = {
   Image => $Image,
   Album => $AlbumName,
   Number=> $Number,
};


$tt->process('picturesDisplay.tt', $vars)
    || die $tt->error(), "\n";


1;