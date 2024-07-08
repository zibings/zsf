export function deepCopy(inObject) {
	let outObject, value, key;

	if (typeof inObject !== "object" || inObject === null) {
		return inObject;
	}

	if (inObject instanceof Date) {
		return new Date(inObject.valueOf());
	}

	outObject = Array.isArray(inObject) ? [] : {};

	for (key in inObject) {
		value = inObject[key];

		outObject[key] = deepCopy(value);
	}

	return outObject;
}
