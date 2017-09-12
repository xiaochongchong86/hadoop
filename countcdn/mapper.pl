#!/user/bin/perl
my %hash;
while(<STDIN>)
{
	#next unless m/.*btime.com.*/ig;
        my @list = split(/ /,$_);
        my $tm = substr($list[4],1,-3);
	if ($tm and $list[10])
	{
	    print $tm ,"\t",$list[10],"\n";
	}
        #$hash{'btime'}{$tm} += $list[10];

}


                                 
