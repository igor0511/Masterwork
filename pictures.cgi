#!/usr/bin/perl

print "Content-type:text/html\r\n\r\n";


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
  
  # Read a file
  my $fd = $smb->open("smb://192.168.0.14/Public/", '0666');

  my $fd = $smb->opendir("smb://192.168.0.14/Public/Shared\ Pictures/");
  while (my $f = $smb->readdir_struct($fd)) {
  # only work with directories
   if ($f->[0] == SMBC_DIR) {
     push @AvailableFolders , $f->[1];
  }
 # elsif ($f->[0] == SMBC_FILE) {
  #   print "File ",$f->[1],"\n";
 # }
  }
 #close($fd);

my $vars = {
   Folders => \@AvailableFolders,   
};


$tt->process('pictures.tt', $vars)
    || die $tt->error(), "\n";


1;