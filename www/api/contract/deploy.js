const run = async() =>
{
    const network = 'ropsten.';
    const address = '0x8EB624954FFaaDF37f38c27D857bE29f08E3EBD9';
    const private_key = '0x5972666ef52e11b25fab85524cd1b9c0d928f3930f2f54482e3acaf5f9f4774b';
    const args = [1001,"10000000000000000","0xca35b7d915458ef540ade6068dfe2f44e8fa733c","0xca35b7d915458ef540ade6068dfe2f44e8fa733c","0x14723a09acff6d2a60dcdf7aa4aff308fddc160c","0x4b0897b0513fdc7c541b6d9d7e929c4e5364d2db",5,1];

    let Web3 = require('web3'); 
    // let Tx = require('ethereumjs-tx');
    const solc = require('solc');

    const fs = require('fs');
    const path = require('path');
    const sol = fs.readFileSync(path.join(__dirname, 'src/LianMiGroupLite.sol'), 'utf8');
    //const sol = fs.readFileSync(path.join(__dirname, 'src/LianMiTest.sol'), 'utf8');
    const output = solc.compile(sol, 1); // 1 activates the optimiser
    let abi;
    let bytecode;
    for (const contractName in output.contracts) 
    {
        abi = JSON.parse(output.contracts[contractName].interface);
        bytecode = output.contracts[contractName].bytecode;
    }

    //console.log( abi );
    //console.log( bytecode );

    
            
    let web3 = new Web3(
        new Web3.providers.HttpProvider('https://'+network+'infura.io/bSF7tAgB2AvsYgmBA4eo')
        // new Web3.providers.HttpProvider('http://localhost:8545')
    );

    // let privateKey = new Buffer(private_key.substr(2, private_key.length-2), 'hex');



    let gasPrice = await web3.eth.getGasPrice();
    let nonce = await web3.eth.getTransactionCount( address );
    let contractInstance = new web3.eth.Contract(abi);
    
    
    let gaslimit = await contractInstance.deploy({
        data: '0x'+bytecode
    }).estimateGas( {"gas":980000,"from":address,"value":0});

    console.log( gaslimit );

    let deploy = contractInstance.deploy({
        data: bytecode,
        arguments: null
    }).encodeABI();

    // 采用 web3.eth.accounts.signTransaction 签名

    let transactionObject = {
        gas: web3.utils.numberToHex( gaslimit+100000 ),
        gasPrice: web3.utils.numberToHex( gasPrice ),
        data: '0x'+deploy,
        from: address,
        value: web3.utils.numberToHex( 0 )
    };

    console.log( transactionObject );

    web3.eth.accounts.signTransaction(transactionObject, private_key, function (error, signedTx) 
    {
        if (error) 
        {
            console.log( error );
        }
        else
        {  
            console.log( signedTx );
            
            web3.eth.sendSignedTransaction(signedTx.rawTransaction).once('transactionHash', (hash) =>
            {
                console.log("in hash\r\n"+hash);
            })
            .on('confirmation', function (number) 
            {
                console.log( number );
            });
        }
    }); 
    
    

    // console.log( nonce );

    // var rawTx = {
    //     nonce: nonce,
    //     gasPrice: web3.utils.numberToHex( gasPrice ),
    //     gasLimit: web3.utils.numberToHex( gaslimit ),
    //     value: '0x00',
    //     data:deploy
    // }

    // console.log( rawTx );

    // var tx = new Tx(rawTx);
    // tx.sign(privateKey);
        
    // var serializedTx = tx.serialize();
        
    // console.log(serializedTx.toString('hex'));  

    // web3.eth.sendSignedTransaction('0x' + serializedTx.toString('hex')).once('transactionHash', (hash) =>{ 
    //     console.log( hash );
    // });
}

run();
