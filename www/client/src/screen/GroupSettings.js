import React, { Component } from 'react';
import { observer , inject } from 'mobx-react';
import { Link } from "react-router-dom";
import { withRouter } from 'react-router-dom';
import { translate } from 'react-i18next';

import Cloumn3Layout from '../component/Cloumn3Layout';
import DocumentTitle from 'react-document-title';
import GroupCard from '../component/GroupCard';
import { toast, showApiError, isApiOk, toInt } from '../util/Function';
import { Button, FormGroup, Intent, Slider } from "@blueprintjs/core";
import Dropzone from 'react-dropzone';
import ReactAvatarEditor from 'react-avatar-editor';
// import Web3 from 'web3';

@withRouter
@translate()
@inject("store")
@observer
export default class GroupSettings extends Component
{
    state = {"group":null,"loaded":false,"loading":false,"image":"/cover.png","scale":1,"cover":""};

    componentDidMount()
    {
       this.loadGroupInfo();
    }

    async loadGroupInfo()
    {
        const { t } = this.props;
        const gid = this.props.match.params.id;
        
        if( toInt( gid )> 0 )
        {
            const { data } = await this.props.store.getGroupDetail( gid );
            if( !showApiError( data , t )  )
            {
                // 检查开启状态
                if( toInt( data.data.is_active ) === 0 )
                {
                    toast(t("栏目不存在或已被关闭"));
                    this.props.history.push("/groups");
                    return false;
                }
                else
                {
                    const image = data.data.cover ? data.data.cover : '/cover.png';
                    this.setState( { "group":data.data,"loaded":true,"image":image } );
                }
                
            }   
        }
    }

    changed( e , name )
    {
        let o = this.state.group;
        o[name] =  e.target.value ;
        this.setState( {"group":o} ) ;
    }

    handleDrop = dropped => 
    {
        this.setState({ "image":dropped[0] });
    }

    update(e)
    {
        const { t } = this.props;
        
        // 检查本地数据的完整性
        if( this.state.group.name.length < 1 )
        {
            toast(t("栏目名称不能为空"));
            return false;
        }
        

        

        // 开始上传封面图片
        this.editor.getImageScaledToCanvas().toBlob( async (blob) => 
        {
            const { data } = await this.props.store.uploadCover( blob );
            if( isApiOk( data ) )
            {
                // 封面图片上传完成，地址为 data.data.url
                
                if( !data.data.url || data.data.url.length < 1 )
                {
                    toast(t("图片上传失败，请重试"));
                    return false;
                }

                const cover = data.data.url;
                // price_wei
                // 调用 store 的 createGroup 方法 
                const result = await this.props.store.updateGroup( this.state.group.id , this.state.group.name , cover  );

            
                if( isApiOk( result.data ) )
                {
                    const data2 = result.data.data;
                    toast(t("设置已成功保存"));

                    this.setState( {"group":data2} );
                }
                else
                    showApiError( result.data , t );    

            }else
                showApiError( data , t );
        } );
    }

    
    render()
    {
        const { t } = this.props;
        const main = this.state.group && <div className="blocklist">
        <div className="groupform">
            <form className="px10list">
                <h1 className="lianmi">{t("修改栏目资料")}</h1>
                <FormGroup label={t("栏目名称")} requiredLabel={<span className="require">{t("必填")}</span>}>
                    <input id="name" type="text" placeholder={t("3个以上字符，全站唯一")}  className="pt-input pt-large pt-fill" value={this.state.group.name} onChange={e=>this.changed(e,"name")} />
                </FormGroup>
                <FormGroup label={t("栏目封面")}>
                    <Dropzone className="dropzone" accept="image/png,image/jpg,image/jpeg,image/gif" multiple={false} onDrop={this.handleDrop}>
                        <p className="covertext">{t("点我选择栏目封面图")}</p>
                    </Dropzone>
                    <div className="editbox">
                        <ReactAvatarEditor ref={(editor) => this.editor = editor} width={150} height={150}  border={1} image={this.state.image} scale={this.state.scale} crossOrigin="anonymous" className="theavatar"/>
                        <Slider
                            className="slider"
                            min={1}
                            max={5}
                            stepSize={0.1}
                            labelRenderer={false}
                            onChange={(value)=>this.setState({"scale":value})}
                            value={this.state.scale}
                            vertical={true}
                            showTrackFill={true}
                        /> 
                    </div>
                </FormGroup>
                
                <FormGroup>
                    <Button text={t("更新")} intent={Intent.PRIMARY}  onClick={(e)=>this.update(e)} large={true} />

                    <Button text={t("返回栏目")} intent={Intent.NONE}  onClick={(e)=>this.props.history.push('/group/'+this.state.group.id)} large={true} className="left5"/>
                </FormGroup>
            </form>
        </div>
    </div>;;
        
        const left = this.state.group && <div className="groupleft px10list">
        <GroupCard group={this.state.group}/></div>;
        
        return <DocumentTitle title={t('栏目设置')+'@'+t(this.props.store.appname)}><Cloumn3Layout left={left} main={main} /></DocumentTitle>;
    }
}