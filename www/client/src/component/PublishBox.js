import React, { Component, Fragment } from 'react';
import { observer , inject } from 'mobx-react';
import { Link } from "react-router-dom";
import { withRouter } from 'react-router-dom';
import { translate } from 'react-i18next';
import { sprintf } from 'sprintf-js';
import {  Popover, PopoverInteractionKind, Position , Icon, TextArea, Button, Intent, Colors, Switch } from "@blueprintjs/core";
import { handeleBooleanGlobal, groupsToId, toast, handeleStringGlobal, showApiError, isApiOk } from '../util/Function';



import Select from 'react-select';
import Dropzone from 'react-dropzone';
import ReactFileReader from 'react-file-reader';

@withRouter
@translate()
@inject("store")
@observer
export default class PublishBox extends Component
{
    
    state = { "togroups":[] }
    
    async publish()
    {
        const store = this.props.store;
        const { t } = this.props;

        
        // 发布前检查数据
        // 内容不能为空
        if( store.draft_text.length < 1 )
        {
            toast(t("内容不能为空"));
            return false;
        }

        // 栏目不能为空
        if( store.draft_groups.length < 1 )
        {
            toast(t("请选择要发布到的栏目"));
            this.props.store.draft_groups_menu_open = true;
            // 自动打开
            return false;
        }

        // 内容附图可以为空，所以这里不处理
        
        // 内容准备好了，调用接口进行发布
        const { data } = await store.publishFeed( store.draft_text , groupsToId( store.draft_groups ) , store.draft_images , { "url":this.props.store.draft_attachment_url , "name":this.props.store.draft_attachment_name} , store.draft_is_paid | 0 );

        // console.log( data );

        if( isApiOk( data ) )
        {
            toast( t("内容发布成功") );
            if( this.props.onFinish ) this.props.onFinish( data );
        }
        else
        {
            showApiError( data , t );
        }
    }

    async update()
    {
        const store = this.props.store;
        const { t } = this.props;

        // 发布前检查数据
        // 内容不能为空
        if( store.draft_text.length < 1 )
        {
            toast(t("内容内容不能为空"));
            return false;
        }

        // 栏目不能为空
        if( store.draft_feed_id < 1 )
        {
            toast(t("修改的内容ID丢失，请刷新页面后重试"));
            return false;
        }

        // 内容附图可以为空，所以这里不处理
        
        // 内容准备好了，调用接口进行发布
        const { data } = await store.updateFeed( store.draft_feed_id , store.draft_text , store.draft_images ,  { "url":this.props.store.draft_attachment_url , "name":this.props.store.draft_attachment_name} , store.user.draft_is_paid | 0 );

        // console.log( data );

        if( isApiOk( data ) )
        {
            toast( t("内容更新成功") );
            if( this.props.onFinish ) this.props.onFinish( data );
            
        }
        else
        {
            showApiError( data , t );
        }
    }

    handleSelect( e )
    {
        this.props.store.draft_groups = e;
    }

    async handleDrop( files )
    {
        const { t } = this.props;
        const len = this.props.store.draft_images.length;

        if( files.length + len > 12 )
        {
            toast(t("一条内容最多只能附带12张图片"));
            return false;
        }

        let uploaded_images = [];

        for( var i = 0 ; i < files.length ; i++ )
        {
            if( files[i].size > 1024*1024*50 )
            {
                toast(this.props.t("文件过大，请不要超过50M"));
                continue;
                //  return false;
            }   
            
            const result = await this.props.store.uploadImage( files[i] );
            
            // 上传成功
            if( result.data.code == 0 )
            {
                uploaded_images.push( result.data.data );
                // toast(t("第"+ (i+1+len) +"张图片上传成功。"));
                
                toast(sprintf(t("第%s张图片上传成功。"), i+1+len));

            }
            else
            {
                toast(sprintf(t("第%s张图片上传失败，请留意图片的大小。"), i+1+len)+result.data.message);
            }   

        }

        
        if( !Array.isArray( this.props.store.draft_images ) )
        this.props.store.draft_images = [];

        this.props.store.draft_images = this.props.store.draft_images.concat( uploaded_images );
          

        // console.log( this.props.store.draft_images );

        // 如果内容内容为空，添加分享图片字样
        if( this.props.store.draft_text.length < 1 )
        {
            this.props.store.draft_text = t("分享图片");
        }
    
    }

    removeImage( thumb_url )
    {
        // array.splice(i, 1);
        let array = [];
        this.props.store.draft_images.map( (item ) => 
        {
            if( item.thumb_url != thumb_url ) array.push(item);
        } );

        this.props.store.draft_images = array;
    }

    async onAttachementSelected( files )
    {
        if( files[0] )
        {
            if( files[0].size > 1024*1024*5 )
            {
                toast(this.props.t("文件过大，请不要超过5M"));
                return false;
            }    
            
            // let  text = await files[0].text();
            // text = text.substring( 0 , 10 );
        
            const { data } = await this.props.store.uploadAttachment( files[0].name , files[0]  );
            if( isApiOk( data ) )
            {
                const { name , url } = data.data;
                this.props.store.draft_attachment_name = name;
                this.props.store.draft_attachment_url = url;
                // console.log( data.data );
                
            }else
                showApiError( data , this.props.t );
        }
    }

    cancelUpdate()
    {
        this.props.onClose();
        this.props.store.cleanUpdate();
    }

    render()
    {
        const { store } = this.props;
        const { t } = this.props;
        let index = 0;

        const options = this.props.groups ? this.props.groups : null;
        const className = this.props.className ? this.props.className : '';
        
        return <div className={"publishbox px10list "+className}>
        <TextArea className="box" placeholder={t("今天有什么好东西分享到栏目？")} value={store.draft_text} onChange={(e)=>handeleStringGlobal( e , 'draft_text' )} maxLength={store.draft_text_max}
        />

        {store.draft_action == 'insert' && <div className="group">
            <Select 
                placeholder={t("请选择栏目，栏主直发，订户投稿需审核")}
                closeMenuOnSelect={true}
                isMulti
                options={options}
                value={store.draft_groups}
                classNamePrefix="groupselect"
                noOptionsMessage={()=>t("没有可用的栏目啦")}
                onChange={(e)=>this.handleSelect(e)}
                menuIsOpen={store.draft_groups_menu_open}
                onMenuClose={()=>{store.draft_groups_menu_open=false}}
                onMenuOpen={()=>{store.draft_groups_menu_open=true}}
            />
        </div>}

        <div className="action">
            <div className="icons" >
                <Dropzone  className="dropimage pointer" accept="image/png,image/jpg,image/jpeg" capture="image/*" maxSize={1024*1024*10} multiple={true} onDrop={(e)=>this.handleDrop(e)}><Icon icon="media" iconSize={20} color={Colors.GRAY4} title={t("请选择要上传的图片（支持 png 和 jpg 文件），最大5M")}/></Dropzone>
                
                
                
                {this.props.store.draft_attachment_url ? 

                <div className="rowicon">
                    <div className="left">
                        <ReactFileReader fileTypes={["audio/*","video/*","application/zip","text/plain"]} handleFiles={(files)=>this.onAttachementSelected(files)} >
                        <Icon icon="paperclip" iconSize={20} color={Colors.BLUE4} className="point" title={t("请选择要上传的附件，支持常见的音频、视频，文本和Zip格式，最大5M")}/>   
                        </ReactFileReader>
                    </div>
                    <div className="right">
                        <Button icon="cross" minimal={true} onClick={()=>this.props.store.clean_attach()}/>
                    </div>
                </div>
                
                : 
                
                <ReactFileReader fileTypes={["audio/*","video/*","application/zip","text/plain"]} handleFiles={(files)=>this.onAttachementSelected(files)} >
                <Icon icon="paperclip" iconSize={20} color={Colors.GRAY4} className="point" title={t("请选择要上传的附件，支持常见的音频、视频，文本和Zip格式，最大5M")}/>
                </ReactFileReader> 
                
                }
                
                
                
                
                
                {/* { false && <Icon icon="paperclip" iconSize={20} color={Colors.GRAY4}/>} */}

            </div> 

            
            
            {store.draft_action == 'insert' && store.draft_is_paid && <div className="type">
                <Switch checked={store.draft_is_paid} label={t("VIP可见")} large={true} onChange={(e)=>handeleBooleanGlobal(e , 'draft_is_paid')} />
            </div>}

            {store.draft_action == 'insert' && !store.draft_is_paid && <div className="type">
                <Switch checked={store.draft_is_paid} label={t("订户可见")} large={true} className="gray5" onChange={(e)=>handeleBooleanGlobal(e , 'draft_is_paid')} />
            </div>}

            
            <div className="button">
                {store.draft_action == 'insert' && ( this.props.onClose ? <Button text={t("发送")} intent={Intent.PRIMARY} large={true} onClick={()=>this.publish()}/> : <Button text={t("发布 or 投稿")} intent={Intent.PRIMARY} large={true} onClick={()=>this.publish()}/> )
                }
                {store.draft_action == 'update' &&
                <Button text={t("更新")} intent={Intent.PRIMARY} large={true} onClick={()=>this.update()}/>}
                
                { this.props.onClose && <Button text={t("取消")} intent={Intent.NONE} large={true} className="left5" onClick={()=>this.cancelUpdate()}/> }
            </div>

            
        </div>
        {/* action end */}

        { Array.isArray( store.draft_images ) && store.draft_images.length > 0 && <div className="uploadedimages">
        { store.draft_images.map( (image) => <div key={index++} className="upimagebox"><img src={image.thumb_url} onClick={()=>window.open(image.orignal_url)} /><Icon icon="small-cross"  className="imagecross pointer" iconSize={18} color={Colors.GRAY3} onClick={()=>this.removeImage(image.thumb_url)} title={t("删除这张图片")}/></div> ) }        
        </div> }
        

        </div>;
    }
}