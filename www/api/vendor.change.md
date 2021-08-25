web3 默认的超时时间太短了，而且没有提供接口，只好改源码了
vendor/sc0vu/Web3.php/src/Contract.php 111行
$requestManager = new HttpRequestManager($provider);
↓
$requestManager = new HttpRequestManager($provider,10);