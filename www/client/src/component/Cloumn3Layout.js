import React, { Component } from 'react';
import { observer , inject } from 'mobx-react';
import { Link } from "react-router-dom";
import { withRouter } from 'react-router-dom';
import { translate } from 'react-i18next';

import Header from './Header';
import ScrollTopView from './ScrollTopView';
import FloatEditor from '../component/FloatEditor'; 
import ImBox from '../component/ImBox'; 


@translate()
@inject("store")
@withRouter
@observer
export default class Cloumn3Layout extends Component
{
    
    componentDidMount()
    {
        let left_is_empty = true;

        window.document.querySelectorAll(".leftside > *").forEach( item =>{
            if( window.getComputedStyle(item).display != "none"  )  left_is_empty = false;
        })

        if( left_is_empty && window.innerWidth <= 600 ) 
        {
            window.document.querySelector(".leftside").style.display = "none";
            window.scrollTo(0, 0); 

        }
    }
    
    render()
    {
        const { left , right , main } = this.props;
        const show_im = this.props.store.im_open;

        
        return <div className="clo3" ><FloatEditor/>
        <Header />
        <div className="middle">
            <div className="contentbox">
                <div className="leftside">
                    {left}
                </div>
                <div className="main">
                    {main}
                </div>
                <div className="rightside">
                    {right}
                </div>
            </div>
        </div>
        { show_im && <ImBox key={1024} /> }
    </div>;
    }
}