import AppState from '../store/AppState';
import AppToaster from './Toaster';
import { sprintf } from 'sprintf-js';
//const t = i18n.getI18nTranslate();

//translate.setI18n(i18n);


export function handeleBooleanGlobal( e , name ) {
    if( name.indexOf('.') > 0 )
    {
        const infos = name.split('.');
        if( AppState[infos[0]] ) AppState[infos[0]][infos[1]] = e.target.checked;
        if( infos[0] === 'user' ) AppState.saveData();
    }
    else
        AppState[name] = e.target.checked;
    
        // console.log(AppState);
}

export function is_fo_address( name ) {
    return name.match( /^[a-z1-5]{12}$/) !== null;
}


export function handeleStringGlobal( e , name ) {
    return AppState[name] = e.target.value;
}

export function toast( string ) {
    AppToaster.show({ "message": string});
}

export function toInt( string )
{
    return parseInt( string , 10 );
}

export function showApiError( data , t )
{
    if( data && data.code && parseInt( data.code , 10 ) !== 0 )
    {
        if( data.args )
            toast( sprintf( t(data.info)  , ...data.args) );
        else
            toast( t(data.message) );   
            
        if( parseInt( data.code , 10 ) === 40301 )
            window.location = '/login';

        return true;    
    }
    else
        return false;
}

export function isApiOk( data )
{
    return parseInt( data.code , 10 ) === 0;
}


export function inGroup( id , groups ) {
    let ret = false;
    groups.forEach(( group ) =>
    {
        if( parseInt( group.value, 10 ) === parseInt( id, 10 ) ) ret = true;
    } );
    return ret;
}

export function groupsToId( groups )
{
    if( !Array.isArray( groups) ) return false;
    
    let ids = [];
    groups.forEach(( group ) =>
    {
        ids.push( parseInt( group.value, 10 ) );
    } );

    return ids;
}

export function strip(html)
{
    var doc = new DOMParser().parseFromString(html, 'text/html');
    return doc.body.textContent || "";
 }

// export const readFile = (inputFile) => {
//     const temporaryFileReader = new FileReader();
  
//     return new Promise((resolve, reject) => {
//       temporaryFileReader.onerror = () => {
//         temporaryFileReader.abort();
//         reject(new DOMException("Problem parsing input file."));
//       };
  
//       temporaryFileReader.onload = () => {
//         resolve(temporaryFileReader.result);
//       };
//       temporaryFileReader.readAsBinaryString(inputFile);
//     });
// };

