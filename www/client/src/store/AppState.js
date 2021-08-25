import { observable, action, computed } from "mobx";
import axios from 'axios';
import { saveAs } from 'file-saver';

const API_BASE = process.env.REACT_APP_API_BASE;
const STORAGE_KEY = 'lianmi2';

class AppState
{
    lianmi_contract = process.env.REACT_APP_CONTRACT;
    
    @observable appname = "NotOnlyFans";  
    @observable domain = "notonlyfans.vip";  
    

    @observable draft_viponly = true;
    @observable draft_images = [];
    @observable draft_attachment_name = false;
    @observable draft_attachment_url = false;
    @observable draft_text = '';
    @observable draft_groups = [];
    @observable draft_text_max = 20000; // 最长两万个字符
    @observable draft_groups_menu_open = false;
    @observable draft_feed_id = 0;
    @observable draft_is_paid = false;
    @observable draft_update_callback = null;

    @observable im_open = false;
    @observable im_to_uid = -1;
    @observable im_position = { x: window.innerWidth/2-200 ,y:50 };

    
    @computed get draft_action ()
    {
        return this.draft_feed_id > 0 ? 'update' : 'insert' ;
    } 

    @observable float_editor_open = false;

    
    

    
    @observable count = 100; 
    @observable user = {
        'token' : '',
        'email' : '',
        'username' : '',
        'nickname' : '',
        'level' : 1,
        'uid':0,
        'id':0,
        'admin_groups':[],
        'vip_groups':[],
        'groups':[],
        'avatar':'',
        'feed_count':0,
        'group_count':0,
        'up_count':0,
        'timeline':'',
        'publish_as_paid':false
    }

    

    

    constructor()
    {
        const user = JSON.parse( localStorage.getItem( STORAGE_KEY+'.user' ));
        if( user ) this.user = user;

        
    }

    @action openIm( uid )
    {
        this.im_to_uid = uid;
        this.im_open = true;
    }


    @action plus()
    {
        this.count++;
        this.user.group_count++;
    }

    @action clean_attach()
    {
       this.draft_attachment_name = false;
       this.draft_attachment_url = false;
    }

    saveData()
    {
        localStorage.setItem( STORAGE_KEY+'.user' , JSON.stringify( this.user ) );
        console.log("save it");

    }

    @action async register( email , nickname , username , password , address = '' )
    {
        var params = new URLSearchParams();
        params.append("email" , email);
        params.append("nickname" , nickname);
        params.append("username" , username);
        params.append("password" , password);
        params.append("address" , address);

        return await axios.post( API_BASE + 'user/register' , params );
    }

    async uploadCover( blob )
    {
        let formData = new FormData();
        formData.append("image", blob );
        formData.append("token", this.user.token);
        return await axios.post( API_BASE + 'image/upload', formData, {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
        })
    }

    async uploadAttachment( name , blob )
    {
        let formData = new FormData();
        formData.append("attach", blob );
        formData.append("name", name );
        formData.append("token", this.user.token);
        return await axios.post( API_BASE + 'attach/upload', formData, {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
        })
    }

    async uploadImage( blob )
    {
        let formData = new FormData();
        formData.append("image", blob );
        formData.append("token", this.user.token);
        return await axios.post( API_BASE + 'image/upload_thumb', formData, {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
        })
    }

    async getGroupDetail( id )
    {
        return await  axios.post( API_BASE + 'group/detail/'+id );
    }

    async getFeedDetail( id )
    {
        var params = new URLSearchParams();
        params.append("token", this.user.token);
        return await  axios.post( API_BASE + 'feed/detail/'+id , params  );
    }

    async groupSetTop( group_id , feed_id , status )
    {
        var params = new URLSearchParams();
        params.append("token", this.user.token);
        params.append("status", status);
        params.append("group_id", group_id);
        params.append("feed_id", feed_id);
        return await  axios.post( API_BASE + 'group/top' , params  );
    }

    async getUnreadCount()
    {
        var params = new URLSearchParams();
        params.append("token", this.user.token);
        return await  axios.post( API_BASE + 'message/unread' , params  );
    }

    async saveFeedComment( id , text )
    {
        var params = new URLSearchParams();
        params.append("token", this.user.token);
        params.append("text", text);
        return await  axios.post( API_BASE + 'feed/comment/'+id , params  );
    }

    async removeFeedComment( id  )
    {
        var params = new URLSearchParams();
        params.append("token", this.user.token);
        params.append("id", id);
        return await  axios.post( API_BASE + 'comment/remove' , params  );
    }

    async getFeedComments( id , since_id = 0 )
    {
        var params = new URLSearchParams();
        params.append("token", this.user.token);
        params.append("since_id", since_id);
        return await  axios.post( API_BASE + 'feed/comment/list/'+id , params  );
    }
    

    async getUserDetail( id )
    {
        var params = new URLSearchParams();
        params.append("token", this.user.token);
        return await  axios.post( API_BASE + 'user/detail/'+id , params );
    }

    async updateGroup( id , name , cover )
    {
        
        var params = new URLSearchParams();
        params.append("token", this.user.token);
        params.append("id" , id);
        params.append("name" , name);
        params.append("cover" , cover);

        return await axios.post( API_BASE + 'group/update_settings' , params );
    }

    async createGroup( name , price_wei , author_address , cover  )
    {
        var params = new URLSearchParams();
        params.append("token", this.user.token);
        params.append("name" , name);
        params.append("price_wei" , price_wei);
        params.append("author_address" , author_address);
        params.append("cover" , cover);


        return await axios.post( API_BASE + 'group/create' , params );
    }

    async preorder( groupid )
    {
        var params = new URLSearchParams();
        params.append("token", this.user.token);
        return await axios.post( API_BASE + 'group/preorder/'+groupid ,  params );
    }

    async checkOrder( order_id )
    {
        var params = new URLSearchParams();
        params.append("token", this.user.token);
        params.append("order_id", order_id);
        return await axios.post( API_BASE + 'group/checkorder' ,  params );
    }

    async checkGroupContract( groupid  )
    {
        return await axios.post( API_BASE + 'group/contract/check/'+groupid );
    }

    async checkVipContract( groupid  )
    {
        var params = new URLSearchParams();
        params.append("token", this.user.token);
        return await axios.post( API_BASE + 'group/vip/check/'+groupid , params );
    }

    async updateUserAvatar( image_url )
    {
        var params = new URLSearchParams();
        params.append("token", this.user.token);
        params.append("avatar", image_url);
        
        return await axios.post( API_BASE + 'user/update_avatar' , params );
    }

    async updateUserCover( image_url )
    {
        var params = new URLSearchParams();
        params.append("token", this.user.token);
        params.append("cover", image_url);
        
        return await axios.post( API_BASE + 'user/update_cover' , params );
    }
    
    async updateUserProfile( nickname , address = '' )
    {
        var params = new URLSearchParams();
        params.append("token", this.user.token);
        params.append("nickname", nickname);
        params.append("address", address);
        
        return await axios.post( API_BASE + 'user/update_profile' , params );
    }

    async updateUserPassword( old_password , new_password )
    {
        var params = new URLSearchParams();
        params.append("token", this.user.token);
        params.append("old_password", old_password);
        params.append("new_password", new_password);
        
        return await axios.post( API_BASE + 'user/update_password' , params );
    }

    async updateUserInfo()
    {
        var params = new URLSearchParams();
        params.append("token", this.user.token);
        
        const result =  await axios.post( API_BASE + 'user/self' , params );
        const data = result.data;

        if( data && data.code && parseInt( data.code , 10 ) !== 0 )
        {
            // 
        }
        else
        {
            // 更新user信息
            this.user = result.data.data;
            this.user.uid = result.data.data.id;
            this.saveData();
        }

        return result;
    }

    async joinGroup( groupid )
    {
        var params = new URLSearchParams();
        params.append("token", this.user.token);
        
        return await axios.post( API_BASE + 'group/join/'+groupid , params );
    }

    async quitGroup( groupid )
    {
        var params = new URLSearchParams();
        params.append("token", this.user.token);
        
        return await axios.post( API_BASE + 'group/quit/'+groupid , params );
    }

    async logout()
    {
        var params = new URLSearchParams();
        params.append("token", this.user.token);
        
        return await axios.post( API_BASE + 'logout/' , params );
    }

    async getGroupFeed( groupid , since_id , filter='all' )
    {
        var params = new URLSearchParams();
        params.append("token", this.user.token); // 发布接口需要登入
        params.append("since_id", since_id); 
        params.append("filter", filter); 

        return await axios.post( API_BASE + 'group/feed2/'+groupid , params );
    }

    async getUserBlacklist( since_id=0 )
    {
        var params = new URLSearchParams();
        params.append("token", this.user.token); // 发布接口需要登入
        params.append("since_id", since_id); 
        
        return await axios.post( API_BASE + 'user/blacklist' , params );
    }

    async checkUserInBlacklist( uid )
    {
        var params = new URLSearchParams();
        params.append("token", this.user.token); // 发布接口需要登入
        params.append("uid", uid); 
        
        return await axios.post( API_BASE + 'user/inblacklist' , params );
    }

    async getGroupTop100()
    {
        var params = new URLSearchParams();
        params.append("token", this.user.token); 
        return await axios.post( API_BASE + 'group/top100' , params );
    }

    async getGroupMine()
    {
        var params = new URLSearchParams();
        params.append("token", this.user.token); 
        return await axios.post( API_BASE + 'group/mine' , params );
    }

    async setUserInBlacklist( uid , status )
    {
        var params = new URLSearchParams();
        params.append("token", this.user.token); // 发布接口需要登入
        params.append("uid", uid); 
        params.append("status", status); 
        
        return await axios.post( API_BASE + 'user/blacklist_set' , params );
    }

    async getGroupMember( groupid , since_id , filter='all' )
    {
        var params = new URLSearchParams();
        params.append("token", this.user.token); // 发布接口需要登入
        params.append("since_id", since_id); 
        params.append("filter", filter); 

        return await axios.post( API_BASE + 'group/member/'+groupid , params );
    }

    async getUserFeed( uid , since_id = 0 , filter='all' )
    {
        var params = new URLSearchParams();
        params.append("token", this.user.token);
        params.append("since_id", since_id); 
        params.append("filter", filter); 
        
        return await  axios.post( API_BASE + 'user/feed/'+uid , params );
    }

    async getHomeTimeline( since_id = 0 , filter='all' )
    {
        var params = new URLSearchParams();
        params.append("token", this.user.token);
        params.append("since_id", since_id); 
        params.append("filter", filter); 
        
        return await  axios.post( API_BASE + 'timeline' , params );
    }

    async getTimelineLastId( filter='all' )
    {
        var params = new URLSearchParams();
        params.append("token", this.user.token);
        params.append("filter", filter); 
        
        return await  axios.post( API_BASE + 'timeline/lastid' , params );
    }

    async setGroupBlacklist( group_id , uid , status )
    {
        var params = new URLSearchParams();
        params.append("token", this.user.token); // 发布接口需要登入
        params.append("group_id", group_id); 
        params.append("uid", uid); 
        params.append("status", status); 
        
        return await axios.post( API_BASE + 'group/blacklist' , params );
    }

    async setGroupContributeList( group_id , uid , status )
    {
        var params = new URLSearchParams();
        params.append("token", this.user.token); // 发布接口需要登入
        params.append("group_id", group_id); 
        params.append("uid", uid); 
        params.append("status", status); 
        
        return await axios.post( API_BASE + 'group/contribute_blacklist' , params );
    }

    async setGroupCommentList( group_id , uid , status )
    {
        var params = new URLSearchParams();
        params.append("token", this.user.token); // 发布接口需要登入
        params.append("group_id", group_id); 
        params.append("uid", uid); 
        params.append("status", status); 
        
        return await axios.post( API_BASE + 'group/comment_blacklist' , params );
    }

    

    async getContribute(  since_id = 0 , filter='all' )
    {
        var params = new URLSearchParams();
        params.append("token", this.user.token);
        params.append("since_id", since_id); 
        params.append("filter", filter); 
        
        return await  axios.post( API_BASE + 'group/contribute' , params );
    }

    async updateContribute( group_id, feed_id, status )
    {
        var params = new URLSearchParams();
        params.append("token", this.user.token);
        params.append("group_id", group_id); 
        params.append("feed_id", feed_id); 
        params.append("status", status); 

        return await  axios.post( API_BASE + 'group/contribute/update' , params );
    }

    @action
    async publishFeed( text , group_ids , images , attach ,  is_paid )
    {
        var params = new URLSearchParams();
        params.append("token", this.user.token); // 发布接口需要登入

        params.append("text" , text);
        params.append("groups" ,  JSON.stringify( group_ids ) );
        params.append("images" ,  JSON.stringify( images ) );
        params.append("attach" ,  JSON.stringify( attach ) );
        params.append("is_paid" , parseInt( is_paid , 10 ));
        
        const result = await axios.post( API_BASE + 'feed/publish' , params );
        
        // 当 feed 发布成功，清空 text 和 images
        if( result.data && (parseInt( result.data.code , 10 ) == 0 ))
        {
            this.draft_text = '';
            this.draft_images = [];
            this.draft_groups = [];
            this.draft_attachment_name = false;
            this.draft_attachment_url = false;
        }
        
        return result;
    }

    async removeFeed( id )
    {
        var params = new URLSearchParams();
        params.append("token", this.user.token); // 发布接口需要登入

        return await axios.post( API_BASE + 'feed/remove/'+id , params );
    }

    async download( url , name = 'unkown' )
    {
        // saveAs( url+'?token='+this.user.token , name );

        var params = new URLSearchParams();
        params.append("token", this.user.token); // 发布接口需要登入

        const { data } = await axios.post( url , params , { 'responseType': 'arraybuffer' } );
        saveAs( new Blob([data] ) , name );
    }

    @action
    async updateFeed( id, text , images , attach , is_paid )
    {
        var params = new URLSearchParams();
        params.append("token", this.user.token); // 发布接口需要登入

        params.append("text" , text);
        params.append("images" ,  JSON.stringify( images ) );
        params.append("attach" ,  JSON.stringify( attach ) );
        params.append("is_paid" , parseInt( is_paid , 10 ));
        
        const result = await axios.post( API_BASE + 'feed/update/'+id , params );
        
        // 当 feed 发布成功，清空 text 和 images
        if( result.data && (parseInt( result.data.code , 10 ) == 0 ))
        {
            if( this.draft_update_callback ) 
                this.draft_update_callback( result.data );
            
            this.cleanUpdate();
            
             
        }
        
        return result;
    }

    @action cleanUpdate()
    {
        this.draft_update_callback = null;   

        this.draft_text = '';
        this.draft_images = [];
        this.draft_groups = [];
        this.draft_feed_id = 0;
        this.draft_attachment_name = false;
        this.draft_attachment_url = false;
    }

    async sendMessage( uid , text )
    {
        var params = new URLSearchParams();
        params.append("token", this.user.token);
        params.append("text", text);
        return await  axios.post( API_BASE + 'message/send/'+uid , params  );
    }

    async getMessageHistory( uid , since_id )
    {
        var params = new URLSearchParams();
        params.append("token", this.user.token);
        params.append("since_id", since_id);
        return await  axios.post( API_BASE + 'message/history/'+uid , params  );
    }

    async getMessageGroupList( since_id )
    {
        var params = new URLSearchParams();
        params.append("token", this.user.token);
        params.append("since_id", since_id);
        return await  axios.post( API_BASE + 'message/grouplist' , params  );
    }

    // 

    async getMessageLatestId( uid  )
    {
        var params = new URLSearchParams();
        params.append("token", this.user.token);
        return await  axios.post( API_BASE + 'message/lastest_id/'+uid , params  );
    }

    @action async refreshUser()
    {
        var params = new URLSearchParams();
        params.append("token", this.user.token);

        const result = await axios.post( API_BASE + 'user/refresh' , params );
        // console.log( result );
        
        if( result.data.data && result.data.data.token && result.data.data.token.length > 1 )
        {
            /*
            this.user.token = result.data.data.token;
            this.user.id = result.data.data.id;
            this.user.username = result.data.data.username;
            this.user.nickname = result.data.data.nickname;
            this.user.email = result.data.data.email;
            this.user.level = result.data.data.level;
            this.user.avatar = result.data.data.avatar;
            this.user.groupcount = result.data.data.groupcount;
            this.user.feedcount = result.data.data.feedcount;
            this.user.upcount = result.data.data.upcount;
            */
            this.user = result.data.data;
            this.user.uid = result.data.data.id;
            this.saveData();
        }
            
        
        return result;
    }

    @action async login( email , password )
    {
        var params = new URLSearchParams();
        params.append("email" , email);
        params.append("password" , password);

        const result = await axios.post( API_BASE + 'user/login' , params );
        console.log( result );
        
        if( result.data.data && result.data.data.token && result.data.data.token.length > 1 )
        {
            /*
            this.user.token = result.data.data.token;
            this.user.id = result.data.data.id;
            this.user.username = result.data.data.username;
            this.user.nickname = result.data.data.nickname;
            this.user.email = result.data.data.email;
            this.user.level = result.data.data.level;
            this.user.avatar = result.data.data.avatar;
            this.user.groupcount = result.data.data.groupcount;
            this.user.feedcount = result.data.data.feedcount;
            this.user.upcount = result.data.data.upcount;
            */
            this.user = result.data.data;
            this.user.uid = result.data.data.id;
            this.saveData();
        }
            
        
        return result;    
    }

}

export default new AppState();