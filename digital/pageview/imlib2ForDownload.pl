#!/usr/bin/perl

use Image::Imlib2;

my $input  = $ARGV[0];
my $output = $ARGV[1];
my $x = $ARGV[2];
my $y = $ARGV[3];
my $w = $ARGV[4];
my $h = $ARGV[5];

my $img = Image::Imlib2->load($input);
my $img2 = Image::Imlib2->new;
##$img->load( $input );
$img2 = $img->crop($x,$y,$w,$h);
$img2->save( $output );


exit;