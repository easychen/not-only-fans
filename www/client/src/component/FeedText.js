import React, { Component } from 'react';
import { PropTypes } from 'prop-types';
import { strip } from '../util/Function';
import nl2br from 'react-nl2br';
import Linkify from 'react-linkify';

class FeedText extends Component {

    state = {
        expanded: false
    }

    toggleLines()
    {
        this.setState( { "expanded":!this.state.expanded } );
    }

    render() {
        
        const maxlength = this.props.maxlength ? this.props.maxlength : 300;
        const toolong = this.props.text.length > maxlength ;
        const shorttext = this.props.text.substr( 0 , maxlength );
        const longtext = this.props.text;
        const more = this.props.more ? this.props.more : 'more';
        const less = this.props.less ? this.props.less : 'less';
        const className = this.props.className ? this.props.className : '';

        return (
            <div className="feedtextcomponent">
                
                { toolong && <div>
                    { !this.state.expanded && <div>
                        <Linkify properties={{target: '_blank'}}>{nl2br(shorttext)}</Linkify>
                        <span className={className}>... <a onClick={()=>this.toggleLines()}>{more}</a></span>
                    </div> }

                    { this.state.expanded && <div>
                        <Linkify properties={{target: '_blank'}}>{nl2br(longtext)}</Linkify> <span className={className}><a onClick={()=>this.toggleLines()}>{less}</a></span>
                    </div> }       
                </div>}

               { !toolong && <div><Linkify properties={{target: '_blank'}}>{nl2br(longtext)}</Linkify></div>}
                
            </div>
        );
    }
}

export default FeedText;