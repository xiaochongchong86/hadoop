#!/usr/bin/perl
#
my @traff;
#while(<STDIN>)
while(<>)
{
	chomp;
	my @list = split('\t',$_);
	my @tm = split(':', $list[0]);
        my $min = $tm[-2];
	unless  ( $min % 5 )
	{
		push @traff, int( $list[1] ) if $tm[-1] eq "00";
	}
}
@traff =   sort { $a <=> $b }  @traff;
my $index = int( @traff *  0.95 ) - 1 ;
print $index,"\n";
print $traff[$index]  /1024 /1024 * 8,"\n";
