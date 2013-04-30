define( [ ],
  function( ) {
    return {
      obj: {
        /**
         * Taken from: http://stackoverflow.com/questions/1068834/object-comparison-in-javascript
         * (slightly modified to match style and lint more correctly)
         * @param  {obj}     firstObj  first object to compare
         * @param  {obj}     secondObj object to compare firstObj to
         * @return {boolean} true on match, false otherwise
         */
        compare: function( firstObj, secondObj ) {
          var p;
          for( p in firstObj ) {
            if( typeof( secondObj[p] ) === 'undefined' ) {
              return false;
            }
          }

          for( p in firstObj ) {
            if( firstObj[ p ] ) {
              switch( typeof( firstObj[p] ) ) {
                case 'object':
                  if( !firstObj[p].equals( secondObj[p] ) ) {
                    return false;
                  }
                  break;
                case 'function':
                  if( typeof secondObj[p] === 'undefined' || ( p !== 'equals' && firstObj[p].toString() !== secondObj[p].toString() ) ) {
                    return false;
                  }
                  break;
                default:
                  if( firstObj[p] !== secondObj[p] ) {
                    return false;
                  }
              }
            } else {
              if( secondObj[p] ) {
                return false;
              }
            }
          }

          for( p in secondObj ) {
            if( typeof firstObj[p] === 'undefined' ) {
              return false;
            }
          }

          return true;
        }
      }
    };
  }
);