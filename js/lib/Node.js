/**
 * A utility class to 
 * better handle automatic
 * static content creation.
 */
class Node{
    constructor( value){
        this._value = value;
        this._parent = null;
        this._children = [];
    }

    addChild(node){
        if(! node instanceof Node)
            throw 'Child node must be a node object.';
        node._parent = this;
        this._children.push(node);
    }

    removeChildren(){
        this._children = [];
    }

    setParent(parent){
        if(! node instanceof Node)
            throw 'Parent node must be a node object';
        this._parent = parent;
        parent.addChild(this);
    }

    getChildren(){
        return this._children;
    }

    getParent(){
        return this._parent;
    }
    /**
     * @returns an HTML string representing the node object according to settings.
     * @argument settings : an array containing
     * - HTML tag
     * - class
     * - id
     * with the array index matching the tree's element's depth.
     */
    toHTML(settings){
        str = '';

    }

}

try{
    let node = new Node('hello');
    node.addChild(new Node('world'));
    console.log('lelo');
    console.log(node);
    console.log(node.getChildren[0]);


}catch(e){
    console.log(e);
}