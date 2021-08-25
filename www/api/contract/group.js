const run = async() =>
{
    const network = 'ropsten.';
    const contract_address = '0xf6351b9af2da7f8613c6763b42feedae6441f309';
    const address = '0xF05949e6d0Ed5148843Ce3f26e0f747095549BB4';
    const private_key = '0xBD51B51B1374B000FCAE30CB9E35072C88A64134FC60ED3A7D3D6CE6E02C1636';
    
    let Web3 = require('web3'); 
    const fs = require('fs');
    const path = require('path');
    const abi = JSON.parse( fs.readFileSync(path.join(__dirname, 'build/lianmi.abi'), 'utf8'));
    

    let web3 = new Web3(
        new Web3.providers.HttpProvider('https://'+network+'infura.io/d4b5f8a729cc491e97d91f1180030623')
        // new Web3.providers.HttpProvider('http://localhost:8545')
    );

    var argv = require('minimist')(process.argv.slice(2),{"string":['author_address','seller_address','price']});
    
    const { groupid , price , author_address , seller_address } = argv;
    const author_rate = 94;
    //const seller_address = address;
    const seller_rate = Web3.utils.isAddress( seller_address ) && author_address != seller_address  ? 1 : 0;
   
    if( parseInt( groupid , 10 ) < 1 || !price || !Web3.utils.isAddress( author_address ) )
    {
        console.log( 'error' );
        return false;
    }
    
    
    let gasPrice = await web3.eth.getGasPrice();
    let nonce = await web3.eth.getTransactionCount( address );
    let contractInstance = new web3.eth.Contract( abi , contract_address );
    
    /**
     * uint groupid, 
        uint price,
        address author_address, 
        uint16 author_rate,
        address seller_address,
        uint16 seller_rate
    */
    
    const function_data = contractInstance.methods.setGroup( groupid , price , author_address , author_rate , seller_address , seller_rate ).encodeABI();

    // console.log( gaslimit );
    const gaslimit = 530000;

    // 采用 web3.eth.accounts.signTransaction 签名

    let transactionObject = {
        gas: web3.utils.numberToHex( gaslimit ),
        gasPrice: web3.utils.numberToHex( gasPrice ),
        data: function_data,
        from: address,
        to:contract_address,
        value: web3.utils.numberToHex( 0 )
    };

    //console.log( transactionObject );

    //return false;

    web3.eth.accounts.signTransaction(transactionObject, private_key, function (error, signedTx) 
    {
        if (error) 
        {
            console.log( "error" );
        }
        else
        {  
            //console.log( signedTx );
            
            web3.eth.sendSignedTransaction(signedTx.rawTransaction).once('transactionHash', (hash) =>
            {
                console.log("hash\r\n"+hash);
            })
            .once('receipt', function (receipt) 
            {
                console.log( "ok" );
            }).then().catch();
        }
    }); 
    
    

}

try{
    return run();
}catch( e )
{

};