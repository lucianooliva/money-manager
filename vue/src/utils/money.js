export default {
  sumFromArrayOfObjects: (array, attr) => {
    const sum = array.reduce(
      (sum, item) => sum + Math.floor(parseFloat(item[attr]) * 100),
      0
    )
    return Math.floor( sum / 100 )
  },
  convertMoneyValueToStringPtBr: (val) => {
    let result = val
    if (typeof val == 'number') {
      result = val.toFixed(2).toString()
    }
    result = result.replace(/\./, ',')
    return result
  }
}