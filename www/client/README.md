# NotOnlyFans前端说明文档

修改hooks。

## 添加页面

### 在 component 中添加页面，比如 index.js

```
import React, { Component } from 'react';
import { observer , inject } from 'mobx-react';
import { Redirect } from 'react-router-dom';

@inject("store")
@observer
export default class Index extends Component
{
    render()
    {
        return <p>Welcome to {this.props.store.appname}</p>;
    }
} 
```


### 在 App.js 中 import 它

```
import Index from './component/Index';
```

### 并添加到 Switch 标签里边

```
<Route path="/index" component={Index} />
```

这样就能访问到对应的页面了

## 添加全局数据和方法

只供单个组件使用的数据写到 state 里边即可；多个组件用的数据写到 store/AppState.js 里边。

```
@observable var-you-added = "EasyStarter";  
```

之后可以直接在组件中调用：

```
{this.props.store.var-you-added}
```

对网络请求、文件写入等异步操作，应该全部写入到 AppState.js 中。需要使用 @action 修饰符，建议使用 async/await 来处理异步。

```
@action 
    async get_resume( id )
    {
        var params = new URLSearchParams();
        params.append("id" , id);
        const { data } = await axios.post( 'http://o.ftqq.com/?m=resume&a=detail' , params );

        if( parseInt( data.code , 10 ) === 0  )
        {
            this.current_resume_id = data.data.id;
            this.current_resume_title = data.data.title;
            this.current_resume_content = data.data.content;
        }
        return data ;
    }
```



