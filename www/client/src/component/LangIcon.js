import React, { Component } from 'react';
import { observer , inject } from 'mobx-react';
import { Menu , MenuItem, Popover, PopoverInteractionKind, Position } from "@blueprintjs/core";
import { translate } from 'react-i18next';


@translate()
@observer
export default class LangIcon extends Component
{
    constructor( props )
    {
        super( props );
        this.state = {"langtext":"English"};
    }

    componentDidMount()
    {
        // console.log( "load now" , this.props.i18n.language );
        // const lang = window.localStorage.getItem('');
        
        this.changeText( this.props.i18n.language);
        // this.changeText( 'zh-tw' );
        // this.props.i18n.changeLanguage( 'zh-tw' );
        // 更新路径
    }

    changeText( lang )
    {
        if( lang === 'zh-CN' ) this.setState( {"langtext":"简体中文"} );
        if( lang === 'zh-TW' ) this.setState( {"langtext":"繁體中文"} );
        if( lang === 'en-US' ) this.setState( {"langtext":"English"} );
        if( lang === 'jp' ) this.setState( {"langtext":"日本語"} );
    }

    changeLanguage( lang )
    {
        this.props.i18n.changeLanguage( lang );
        this.changeText( lang );
    }



    render()
    {
        return <Popover {...this.props} content={<Menu> 
            <MenuItem icon="translate"  text="繁體中文" onClick={()=>this.changeLanguage('zh-TW')} />
            <MenuItem icon="translate"  text="简体中文" onClick={()=>this.changeLanguage('zh-CN')} />
            <MenuItem icon="translate"  text="English" onClick={()=>this.changeLanguage('en')}/> 
            <MenuItem icon="translate"  text="日本语" onClick={()=>this.changeLanguage('jp')}/>   
        </Menu>} position={Position.BOTTOM} interactionKind={PopoverInteractionKind.CLICK}>
        <span className="langicon">{this.state.langtext}</span>
        </Popover>
    }

}