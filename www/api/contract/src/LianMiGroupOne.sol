pragma solidity ^0.4.23;

// LianMi栏目分成合约，全部栏目公用一个合约。好处是可以单次部署，购买栏目只需要调用接口（费用更低），以后更新合约也更为简单。
// 注意这个合约没有使用 safemath
contract LianMiGroupOne
{
    event GroupMembershipSold(uint indexed groupid, uint indexed uid, uint price_wei); // Event

    bool is_active = true;
    // ========= 基本账号信息 ================
    // 管理账号和平台分账地址都由莲米控制，但管理账号只管理不收费；平台账号只收费不管理。从而避免风险 
    address public admin; // 管理账号
    address public platform; // 平台分账地址 

    // ========= 栏目订户信息 ================
    //       ↓ groupid          ↓ uid  ↓ timestamp
    mapping( uint => mapping ( uint => uint ) ) public memberOf ;
    
    // ========= 栏目创建费信息 ================
    //       ↓ groupid  ↓ fee
    mapping( uint => uint ) public feeOf ;
    

    
    
    // ========= 栏目分账信息 ================
    struct Settings 
    {
        bool is_active ;
        uint price_wei ; // 栏目定价
        address author; // 作者分成地址
        uint author_percent; // 作者分成比例
        address seller; // 销售分成地址
        uint seller_percent; // 销售分成比例
    }
    //       ↓ groupid
    mapping( uint => Settings ) public settingsOf ;
    

    function mul(uint256 a, uint256 b) internal pure returns (uint256 c) 
    {
        if (a == 0) 
        {
            return 0;
        }

        c = a * b;
        assert(c / a == b);
        return c;
    }

    function sub(uint256 a, uint256 b) internal pure returns (uint256) 
    {
        assert(b <= a);
        return a - b;
    }

    function add(uint256 a, uint256 b) internal pure returns (uint256 c) 
    {
        c = a + b;
        assert(c >= a);
        return c;
    }

  
    modifier admin_only()
    {
        require(msg.sender == admin);
        _;
    }

    // admin_only 的方法，加 when_active 没有意义，因为 admin 可以重置 active 状态
    modifier when_active()
    {
        require(is_active);
        _;
    }

    constructor() public payable
    {
        // 部署合约的人自动成为管理员
        admin = msg.sender;
    }

    // ========= 创建栏目 ================ 
    // 此操作只能由管理员发起
    function setGroup( 
        uint groupid, 
        uint price,
        address author_address, 
        uint16 author_rate,
        address seller_address,
        uint16 seller_rate
         ) public admin_only
    {
        // 作者和销售额分成不能超过100
        require(add(author_rate,seller_rate) <= 100);
        
        // 保存设置
        settingsOf[groupid] = Settings(true, price, author_address, author_rate, seller_address, seller_rate);
    }

    // ========= 修改栏目价格 ================ 
    // 只有栏目作者可以修改栏目价格
    function updateGroupPrice( uint groupid, uint new_price_wei ) public when_active
    {
        require(msg.sender == settingsOf[groupid].author);
        settingsOf[groupid].price_wei = new_price_wei;
    }

    // ========= 修改栏目作者地址 ================ 
    // 只有栏目作者可以修改作者地址
    function updateGroupAuthor( uint groupid, address new_address ) public when_active
    {
        require(msg.sender == settingsOf[groupid].author);
        settingsOf[groupid].author = new_address;
    }

    // ========= 减小栏目作者比例 ================ 
    // 只有栏目作者可以减小栏目作者比例
    function decreaseGroupAuthorPercent( uint groupid, uint16 new_percent ) public when_active
    {
        require(msg.sender == settingsOf[groupid].author);
        require(new_percent < settingsOf[groupid].author_percent);
        settingsOf[groupid].author_percent = new_percent;
    }

    // ========= 修改栏目销售地址 ================ 
    // 只有栏目销售可以修改自己的地址
    function updateGroupSeller( uint groupid, address new_address ) public when_active
    {
        require(msg.sender == settingsOf[groupid].seller);
        settingsOf[groupid].seller = new_address;
    }

    // ========= 减小栏目销售分成比例 ================ 
    // 只有销售可以减小自己的比例
    function decreaseGroupSellerPercent( uint groupid, uint16 new_percent ) public when_active
    {
        require(msg.sender == settingsOf[groupid].seller);
        require(new_percent < settingsOf[groupid].seller_percent);
        settingsOf[groupid].seller_percent = new_percent;
    }

    // ======== 管理员可以增加作者的分成 =============
    function increaseGroupAuthorPercent( uint groupid, uint16 new_percent ) public admin_only
    {
        require(new_percent > settingsOf[groupid].author_percent);
        require(add(new_percent, settingsOf[groupid].seller_percent) <= 100);
        settingsOf[groupid].author_percent = new_percent;
    }

    // ======== 管理员可以增加销售的分成 =============
    function increaseGroupSellerPercent( uint groupid, uint16 new_percent ) public admin_only
    {
        require(new_percent > settingsOf[groupid].seller_percent);
        require(add(new_percent, settingsOf[groupid].author_percent) <= 100);
        settingsOf[groupid].seller_percent = new_percent;
    }

    function updateAdminAddress( address new_address ) public admin_only
    {
        admin = new_address;
    }

    function updatePlatformAddress( address new_address ) public admin_only
    {
        platform = new_address;
    }

    function updateGroupActivity( uint groupid, bool new_value ) public admin_only
    {
        settingsOf[groupid].is_active = new_value;
    }

    // 这个是全局开关，一定不能加 when_active， 不然就再也没法启用了
    function updateContractActivity( bool new_value ) public admin_only
    {
        is_active = new_value;
    }
    
    // 创建栏目付费记录
    // 可以多次付费
    function createGroup( uint groupid ) public payable
    {
        require(msg.value > 0);
        assert(feeOf[groupid] >= 0);
        feeOf[groupid] = add(feeOf[groupid], msg.value);
    }

    // ========= 购买栏目VIP订户 ================ 
    // 此操作每个用户都可以发起
    function buyGroupMembership
    (
        uint groupid,
        uint uid
    ) public payable when_active
    {
        // 栏目的定价必须大于零
        require(settingsOf[groupid].price_wei > 0);

        // 支付的eth 必须大于等于当前定价
        require(msg.value >= settingsOf[groupid].price_wei);

        // 栏目必须存储于可购买状态
        require(settingsOf[groupid].is_active);

        // 从未购买过
        if( memberOf[groupid][uid] < 1 )
            memberOf[groupid][uid] = add(now, 365 days); // 当前时间后一年
        else 
            memberOf[groupid][uid] = add(memberOf[groupid][uid], 365 days); // 续费一年  

        // 开始分账
        uint author_percent = settingsOf[groupid].author_percent;
        uint seller_percent = settingsOf[groupid].seller_percent;
        uint platform_percent = sub(100, add(author_percent, seller_percent));

        assert(add(author_percent,add(seller_percent,platform_percent)) == 100);

        // 这里的乘法可能溢出，引入safemath来处理
        uint author_wei = mul(settingsOf[groupid].price_wei,  author_percent) / 100;
        uint platform_wei = mul(settingsOf[groupid].price_wei, platform_percent) / 100;
        uint seller_wei = mul(settingsOf[groupid].price_wei, seller_percent) / 100;

        // // 总金额要小于收到的钱（因为有小数所以可能出错）
        require(add(author_wei,add(platform_wei, seller_wei)) <= settingsOf[groupid].price_wei);

        if( author_wei > 0 ) settingsOf[groupid].author.transfer(author_wei);
        if( seller_wei > 0 ) settingsOf[groupid].seller.transfer(seller_wei);
        if( platform_wei > 0 ) platform.transfer(platform_wei);

        // 发送通知
        emit GroupMembershipSold(groupid, uid, msg.value);   
    }

    // ========= 合约提现 ====
    function getETH() public admin_only
    {
        // 没钱就不用调用了
        require(address(this).balance > 0);
        // 全部转到平台账户里边
        platform.transfer(address(this).balance);
    }

}