pragma solidity ^0.4.23;

contract LianMiGroup 
{
    uint8 public group; // 栏目ID 每个栏目一份独立合约
    uint256 public price_wei; // 栏目定价
    bool public is_closed = false ; // 购买开关

    address public admin; // 管理员，管理员账号也为平台控制，用于日常操作
    address public platform; // 平台账号，用于收钱
    address public author; // 栏目作者
    address public seller; // 销售，可以为空

    uint8 public platform_percent; // 平台分成的百分比
    uint8 public seller_percent; // 销售分成的百分比
    uint8 public author_percent; // 作者分成的百分比

    mapping( uint16 => uint256) public membership; // uid => member timestamp
    
    
    
    // 构造函数，用于初始化
    constructor( 
        uint8 group_id, 
        uint256 price, // 单位是 wei
        address admin_address, 
        address platform_address, 
        address author_address, 
        address seller_address, 
        uint8 platform_rate, 
        uint8 seller_rate ) public payable
    {
        require(platform_rate + seller_rate < 100); // 不能大于100

        // 标识
        group = group_id;
        price_wei = price;
        
        // 地址相关
        admin = admin_address;
        platform = platform_address;
        author = author_address;
        seller = seller_address;

        // 分成相关
        platform_percent = platform_rate;
        seller_percent = seller_rate;
        author_percent = 100 - ( platform_percent + seller_percent );
        assert(platform_percent + seller_percent + author_percent == 100);
    }

    // modifier open_only()
    // {
    //     require(!is_closed);
    //     _;
    // }

    // modifier admin_only()
    // {
    //     require(msg.sender == admin);
    //     _;
    // }

    // modifier author_only()
    // {
    //     require(msg.sender == author);
    //     _;
    // }

    // 开放或者终止订户的购买
    // function toggle_close( bool is_closed_bool ) public admin_only
    // {
    //     is_closed = is_closed_bool;
    // }

    // 管理员可以降低平台分成额度
    // function set_platform_rate( uint new_rate ) public admin_only
    // {
    //     // 新的分成只能比之前低
    //     require(new_rate < platform_percent);

    //     platform_percent = new_rate;
    //     author_percent = 100 - ( platform_percent + seller_percent );
    //     assert(platform_percent + seller_percent + author_percent == 100);
    // }

    // 栏目作者可以降低作者分成额度
    // function set_author_rate( uint new_rate ) public author_only
    // {
    //     // 新的分成只能比之前低
    //     require(new_rate < author_percent);

    //     author_percent = new_rate;
    //     platform_percent = 100 - ( author_percent + seller_percent );
    //     assert(platform_percent + seller_percent + author_percent == 100);
    // }
    
    // 购买栏目订户
    // 参数为 uid = 莲米网站的UID
    // function buy( uint16 uid ) public payable open_only
    // {
    //     // 支付的价格必须大于等于定价
    //     require(msg.value >= price_wei);
        
    //     // 将 uid 和 购买时间写入 member list
    //     // 重复购买将会追加时间
    //     if( membership[uid] < 1 ) 
    //         membership[uid] = now + 365 days; // 当前时间后一年
    //     else     
    //         membership[uid] = membership[uid] + 365 days; // 续一年

    //     // 开始分账
    //     uint author_wei = price_wei * author_percent / 100;
    //     uint platform_wei = price_wei * platform_percent / 100;
    //     uint seller_wei = price_wei * seller_percent / 100;

    //     // 分账百分比要正确
    //     assert(author_percent + platform_percent + seller_percent == 100);
    //     // 总金额要小于收到的钱（因为有小数所以可能出错）
    //     assert(author_wei + platform_wei + seller_wei <= price_wei);

    //     if( author_wei > 0 ) author.transfer(author_wei);
    //     if( platform_wei > 0 ) platform.transfer(platform_wei);
    //     if( seller_wei > 0 ) seller.transfer(seller_wei);
    // }
}