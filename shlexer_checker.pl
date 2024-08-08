#!/usr/bin/perl -w

use strict;
use warnings;


# Run the grep search on the XML file and capture the results. 
my @grep_results = `grep -io \"<w.*>his</w>\" ./the-tempest_TEIsimple_FolgerShakespeare.xml_/the-tempest_TEIsimple_FolgerShakespeare.xml | grep -v \"SD\"`;

# Extract lines from here doc
chomp (my @here_lines = <DATA>);


GREP_LINE: foreach my $grep_line (@grep_results) {

  # Extract the xml:id value from the grep results.
  my $grep_id = "";
  ($grep_id) = $grep_line =~ m/<w xml:id=\"(fs-tmp-\d+)\".*/g;

  # for each line in the here document below, match the $grep_id to each
  # here_id. If there is a match, break the loop. If there is no match
  # print the $grep_id

  foreach my $here_id (@here_lines) {

    if ( $grep_id eq $here_id) {
      next GREP_LINE;
    }
    
  }

  print "$grep_id\n";
}


__DATA__
fs-tmp-0005170
fs-tmp-0005380
fs-tmp-0005510
fs-tmp-0022550
fs-tmp-0027240
fs-tmp-0029980
fs-tmp-0030050
fs-tmp-0030580
fs-tmp-0032070
fs-tmp-0032130
fs-tmp-0032570
fs-tmp-0039980
fs-tmp-0040580
fs-tmp-0045480
fs-tmp-0047380
fs-tmp-0047490
fs-tmp-0047890
fs-tmp-0050770
fs-tmp-0053020
fs-tmp-0063060
fs-tmp-0077150
fs-tmp-0083860
fs-tmp-0087880
fs-tmp-0088020
fs-tmp-0088250
fs-tmp-0095270
fs-tmp-0102780
fs-tmp-0104100
fs-tmp-0108940
fs-tmp-0109280
fs-tmp-0111750
fs-tmp-0112440
fs-tmp-0112530
fs-tmp-0116140
fs-tmp-0116950
fs-tmp-0117200
fs-tmp-0117420
fs-tmp-0123770
fs-tmp-0124870
fs-tmp-0125380
fs-tmp-0136830
fs-tmp-0147770
fs-tmp-0147910
fs-tmp-0148130
fs-tmp-0154130
fs-tmp-0156240
fs-tmp-0157630
fs-tmp-0160310
fs-tmp-0161300
fs-tmp-0167570
fs-tmp-0168160
fs-tmp-0170770
fs-tmp-0170940
fs-tmp-0170990
fs-tmp-0171470
fs-tmp-0181610
fs-tmp-0206520
fs-tmp-0206650
fs-tmp-0212090
fs-tmp-0215150
fs-tmp-0215770
fs-tmp-0219730
fs-tmp-0219870
fs-tmp-0220080
fs-tmp-0220260
fs-tmp-0220880
fs-tmp-0221490
fs-tmp-0222700
fs-tmp-0244390
fs-tmp-0248350
fs-tmp-0259240
fs-tmp-0267810
fs-tmp-0274320
fs-tmp-0283470
fs-tmp-0283570
fs-tmp-0284740
fs-tmp-0297630
fs-tmp-0299210
fs-tmp-0299850
fs-tmp-0299930
fs-tmp-0305390
fs-tmp-0319880
fs-tmp-0331530
fs-tmp-0334060
fs-tmp-0334980
fs-tmp-0335270
fs-tmp-0335600
fs-tmp-0342820
fs-tmp-0345830
fs-tmp-0349710
fs-tmp-0349780
