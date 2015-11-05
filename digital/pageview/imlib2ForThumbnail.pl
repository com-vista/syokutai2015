#!/usr/bin/perl

use Image::Imlib2;

my $input  = $ARGV[0];
my $output = $ARGV[1];
my $w = $ARGV[2];
my $h = $ARGV[3];

my $img = Image::Imlib2->load( $input );
#$img->load( $input );
my $img2 = $img->create_scaled_image($w,$h);
$img2->save( $output );

exit;