#!/usr/bin/perl
#
# 读取hadoop输出  然后   格式 【time    size】，   计算CDN带宽   计算方式取五分钟流量总和 ／300  求出平均流量算一个点   然后将一天中的这些点按照大小排序 取95% 的那个点
my @traff;
my %hash;
my $start;

#按照5分钟的间隔返回时间列表
sub getm()
{
	my @tmlist;
	my @hours = map{ sprintf "%02d", $_ } 0 .. 24;
	my @mins ;
        map{ unless ($_ % 5) { my $min =  sprintf "%02d", $_;push @mins, $min} } 0 .. 55;
        for my $h ( @hours )
	{
		for  my $m ( @mins )
		{
			my $tm = sprintf "%s:%02d:%02d:00",'16/Aug/2017',$h,$m;
			push @tmlist, $tm;
		}
	}	
	return @tmlist;
}
# 解析时间将分钟取出来 模5 然后  减去余数来取五分钟之内的时间key


sub parse()
{
	my $tm = shift;
	my @res = split /:/, $tm;
	my $min = $res[2] eq '00' ? '00' : $res[2] - $res[2] % 5 ;
	$res[2] = $min;
	my $res = sprintf "%s:%s:%02d",$res[0],$res[1],$min;
	return $res;

}

my @tmls = &getm();
my $i = 0;
while(<>)
{
	chomp;
	my @list = split('\t',$_);
	next if $list[0] < '16/Aug/2017:00:00';
	my $tm = &parse( $list[0] );
	$hash{ $tm } += int( $list[1] );
	
}






@traff = values %hash;
@traff =   sort { $a <=> $b }  @traff;
my $index = int( @traff *  0.95 )  ;
print $index,"\n";
print $traff[$index] / 300 /1024 /1024 * 8,"\n";
print $traff[287] / 300 /1024 /1024 * 8,"\n";
print $traff[0] / 300 /1024 /1024 * 8,"\n";
